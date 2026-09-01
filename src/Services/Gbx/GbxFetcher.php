<?php
declare(strict_types=1);

namespace App\Services\Gbx;

use Throwable;

/**
 * Modernized GBX Map Header & Thumbnail parser for TrackMania Forever, ManiaPlanet and TM2020.
 */
class GbxFetcher
{
    private string $filePath;
    private array $data = [];
    private ?string $thumbnailData = null;

    private const GBX_CHALLENGE_TMF = 0x03043000;
    private const GBX_CHALLENGE_TM  = 0x24003000;

    public function __construct(string $filePath = '')
    {
        $this->filePath = $filePath;
        if ($filePath !== '') {
            $this->parse();
        }
    }

    public function parseFile(string $filePath): array
    {
        $this->filePath = $filePath;
        return $this->parse();
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnailData;
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function parse(): array
    {
        $this->data = [
            'uid' => '',
            'name' => basename($this->filePath),
            'author' => 'Unknown',
            'authorNick' => '',
            'environment' => '',
            'mood' => '',
            'authorTime' => 0,
            'bronzeTime' => 0,
            'silverTime' => 0,
            'goldTime' => 0,
            'cost' => 0,
            'hasThumbnail' => false,
        ];
        $this->thumbnailData = null;

        if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
            return $this->data;
        }

        $gbx = @file_get_contents($this->filePath, false, null, 0, 192 * 1024);
        if ($gbx === false || strlen($gbx) < 32) {
            return $this->data;
        }

        try {
            $ptr = 0;
            $len = strlen($gbx);

            $readData = function (int $n) use (&$gbx, &$ptr, $len): string {
                if ($ptr + $n > $len) return '';
                $res = substr($gbx, $ptr, $n);
                $ptr += $n;
                return $res;
            };

            $readInt16 = function () use (&$readData): int {
                $d = $readData(2);
                if (strlen($d) < 2) return 0;
                return unpack('s', $d)[1] ?? 0;
            };

            $readInt32 = function () use (&$readData): int {
                $d = $readData(4);
                if (strlen($d) < 4) return 0;
                return unpack('l', $d)[1] ?? 0;
            };

            $readString = function () use (&$readData, &$readInt32): string {
                $slen = $readInt32() & 0x7FFFFFFF;
                if ($slen <= 0 || $slen > 0x20000) return '';
                return $readData($slen);
            };

            // Magic: 'GBX'
            $magic = $readData(3);
            $version = $readInt16();
            if ($magic !== 'GBX' || $version !== 6) {
                return $this->data;
            }

            // Header block flags: 4 bytes (Format, Compression 'U', BodyTag, Reserved)
            $flags = $readData(4);
            if (strlen($flags) < 4 || $flags[1] !== 'U') {
                return $this->data;
            }

            // Main Class ID: 4 bytes
            $mainClass = $readInt32() & 0xFFFFFFFF;
            if ($mainClass !== self::GBX_CHALLENGE_TMF && $mainClass !== self::GBX_CHALLENGE_TM) {
                return $this->data;
            }

            // Header size: 4 bytes
            $headerSize = $readInt32();
            if ($headerSize <= 0) {
                return $this->data;
            }

            // Number of chunks: 4 bytes
            $numChunks = $readInt32();
            if ($numChunks <= 0 || $numChunks > 64) {
                return $this->data;
            }

            $chunkStart = $ptr;
            $chunkOffset = $chunkStart + ($numChunks * 8);

            $chunks = [];
            for ($i = 0; $i < $numChunks; $i++) {
                $chunkId = $readInt32() & 0x00000FFF;
                $chunkSize = $readInt32() & 0x7FFFFFFF;

                $chunks[$chunkId] = [
                    'off' => $chunkOffset,
                    'size' => $chunkSize,
                ];
                $chunkOffset += $chunkSize;
            }

            // Chunk 0x002: Info Chunk (Times, Cost, Type)
            if (isset($chunks[0x002])) {
                $ptr = $chunks[0x002]['off'];
                $infoVer = ord($readData(1));
                if ($infoVer >= 3) {
                    $readInt32(); // zero
                    $this->data['bronzeTime'] = $readInt32();
                    $this->data['silverTime'] = $readInt32();
                    $this->data['goldTime'] = $readInt32();
                    $this->data['authorTime'] = $readInt32();
                    if ($infoVer >= 4) {
                        $this->data['cost'] = $readInt32();
                    }
                }
            }

            // Chunk 0x003: String Chunk (UID, Environment, Author, Name, Mood)
            if (isset($chunks[0x003])) {
                $ptr = $chunks[0x003]['off'];
                $strVer = ord($readData(1));
                $lookbacks = [];
                $readLookback = function () use (&$readInt32, &$readString, &$lookbacks): string {
                    $index = $readInt32();
                    if ($index === -1) return '';
                    if (($index & 0xC0000000) === 0) {
                        return match ($index) {
                            0xC => 'Canyon',
                            0xCA => 'Storm',
                            0x11 => 'Valley',
                            0x12 => 'Stadium',
                            default => 'Stadium',
                        };
                    }
                    if (($index & 0x3FFFFFFF) === 0) {
                        $str = $readString();
                        $lookbacks[] = $str;
                        return $str;
                    }
                    $lookbackIndex = ($index & 0x3FFFFFFF) - 1;
                    return $lookbacks[$lookbackIndex] ?? '';
                };

                if ($strVer >= 1) {
                    $readInt32(); // lookbacks version (3)
                    $this->data['uid'] = $readLookback();
                    $this->data['environment'] = $readLookback();
                    $this->data['author'] = $readLookback();
                    $name = $readString();
                    if ($name !== '') {
                        $this->data['name'] = $name;
                    }
                    if ($strVer >= 2) {
                        $readLookback(); // kind
                        if ($strVer >= 3) {
                            $readInt32(); // password
                            if ($strVer >= 4) {
                                $this->data['mood'] = $readLookback();
                            }
                        }
                    }
                }
            }

            // Chunk 0x008: Author Chunk (ManiaPlanet & TM2020 Author Nickname, Zone)
            if (isset($chunks[0x008])) {
                $ptr = $chunks[0x008]['off'];
                $authVer = $readInt32();
                if ($authVer >= 1) {
                    $readInt32(); // authorVer
                    $authorLogin = $readString();
                    $authorNick = $readString();
                    $authorZone = $readString();
                    if ($authorNick !== '') {
                        $this->data['authorNick'] = $authorNick;
                        $this->data['author'] = $authorNick;
                    } elseif ($authorLogin !== '' && $this->data['author'] === 'Unknown') {
                        $this->data['author'] = $authorLogin;
                    }
                }
            }

            // Chunk 0x005: XML Chunk
            if (isset($chunks[0x005])) {
                $ptr = $chunks[0x005]['off'];
                $xml = $readString();
                if ($xml !== '') {
                    $this->parseXml($xml);
                }
            }

            // Chunk 0x007: Thumbnail Chunk
            if (isset($chunks[0x007])) {
                $ptr = $chunks[0x007]['off'];
                $chunkSize = $chunks[0x007]['size'];
                $chunkData = substr($gbx, $ptr, $chunkSize);
                $soi = strpos($chunkData, "\xFF\xD8\xFF");
                if ($soi !== false) {
                    $eoi = strpos($chunkData, "\xFF\xD9", $soi);
                    if ($eoi !== false) {
                        $jpgData = substr($chunkData, $soi, $eoi - $soi + 2);
                        if ($jpgData !== '') {
                            $this->thumbnailData = $jpgData;
                            $this->data['hasThumbnail'] = true;
                        }
                    }
                }
            }
        } catch (Throwable) {
            // Ignore format exceptions and return whatever was parsed
        }

        return $this->data;
    }

    private function parseXml(string $xml): void
    {
        libxml_use_internal_errors(true);
        $cleanXml = preg_replace('/&(?!(?:amp|quot|apos|lt|gt);)/', '&amp;', $xml);
        $doc = @simplexml_load_string($cleanXml);
        if (!$doc) return;

        // Header attributes (type: challenge vs map, titlepack, exever)
        if (isset($doc['title'])) {
            $this->data['title'] = (string)$doc['title'];
        }
        if (isset($doc['type'])) {
            $this->data['type'] = (string)$doc['type'];
        }
        if (isset($doc['exever'])) {
            $this->data['exever'] = (string)$doc['exever'];
        }

        if (isset($doc->ident)) {
            if (empty($this->data['uid']) && isset($doc->ident['uid'])) {
                $this->data['uid'] = (string)$doc->ident['uid'];
            }
            if (isset($doc->ident['name'])) {
                $this->data['name'] = (string)$doc->ident['name'];
            }
            if ($this->data['author'] === 'Unknown' || empty($this->data['authorNick'])) {
                if (isset($doc->ident['author'])) {
                    $this->data['author'] = (string)$doc->ident['author'];
                }
            }
        }

        if (isset($doc->desc)) {
            if (empty($this->data['environment']) && isset($doc->desc['envir'])) {
                $this->data['environment'] = (string)$doc->desc['envir'];
            }
            if (empty($this->data['mood']) && isset($doc->desc['mood'])) {
                $this->data['mood'] = (string)$doc->desc['mood'];
            }
            if ($this->data['authorTime'] === 0) {
                $this->data['authorTime'] = (int)($doc->desc['authorscore'] ?? $doc->desc['authortm'] ?? 0);
            }
            if ($this->data['bronzeTime'] === 0) {
                $this->data['bronzeTime'] = (int)($doc->desc['bronzescore'] ?? $doc->desc['bronzetm'] ?? 0);
            }
            if ($this->data['silverTime'] === 0) {
                $this->data['silverTime'] = (int)($doc->desc['silverscore'] ?? $doc->desc['silvertm'] ?? 0);
            }
            if ($this->data['goldTime'] === 0) {
                $this->data['goldTime'] = (int)($doc->desc['goldscore'] ?? $doc->desc['goldtm'] ?? 0);
            }
        }
    }
}
