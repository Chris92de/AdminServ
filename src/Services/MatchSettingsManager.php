<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class MatchSettingsManager
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function listFiles(string $subDir = 'MatchSettings'): array
    {
        $dir = $this->basePath . trim($subDir, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        $items = @scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $dir . $item;
            if (is_file($full) && (str_ends_with(strtolower($item), '.txt') || str_ends_with(strtolower($item), '.xml'))) {
                $files[] = [
                    'name' => $item,
                    'path' => trim($subDir . '/' . $item, '/'),
                    'size' => filesize($full),
                    'modified_at' => filemtime($full),
                ];
            }
        }
        return $files;
    }

    public function parseFile(string $relativePath): array
    {
        $clean = str_replace(['../', '..\\'], '', $relativePath);
        $full = $this->basePath . $clean;

        if (!file_exists($full)) {
            throw new RuntimeException("MatchSettings file not found: {$relativePath}");
        }

        $content = file_get_contents($full);
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($content);
        if (!$doc) {
            throw new RuntimeException("Failed to parse MatchSettings XML.");
        }

        $gameMode = (int)($doc->gameinfos->game_mode ?? 1);
        $scriptName = (string)($doc->gameinfos->script_name ?? '');

        $maps = [];
        if (isset($doc->map)) {
            foreach ($doc->map as $m) {
                $maps[] = [
                    'file' => (string)($m->file ?? ''),
                    'ident' => (string)($m->ident ?? ''),
                ];
            }
        } elseif (isset($doc->challenge)) {
            foreach ($doc->challenge as $c) {
                $maps[] = [
                    'file' => (string)($c->file ?? ''),
                    'ident' => (string)($c->ident ?? ''),
                ];
            }
        }

        $scriptSettings = [];
        if (isset($doc->mode_script_settings->setting)) {
            foreach ($doc->mode_script_settings->setting as $s) {
                $name = (string)($s['name'] ?? '');
                $type = (string)($s['type'] ?? 'text');
                $val = (string)($s['value'] ?? '');
                if ($name !== '') {
                    $scriptSettings[$name] = [
                        'type' => $type,
                        'value' => match ($type) {
                            'boolean' => ($val === '1' || strtolower($val) === 'true'),
                            'integer' => (int)$val,
                            'double', 'float' => (float)$val,
                            default => $val,
                        },
                    ];
                }
            }
        }

        return [
            'file_name' => basename($relativePath),
            'game_mode' => $gameMode,
            'script_name' => $scriptName,
            'map_count' => count($maps),
            'maps' => $maps,
            'mode_script_settings' => $scriptSettings,
        ];
    }

    public function saveMatchSettings(string $filename, array $mapFiles, int $gameMode = 1, string $scriptName = ''): string
    {
        $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
        if (!str_ends_with(strtolower($cleanName), '.txt') && !str_ends_with(strtolower($cleanName), '.xml')) {
            $cleanName .= '.txt';
        }

        $targetDir = $this->basePath . 'MatchSettings' . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $fullPath = $targetDir . $cleanName;

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
        $xml .= "<playlist>\n";
        $xml .= "    <gameinfos>\n";
        $xml .= "        <game_mode>{$gameMode}</game_mode>\n";
        $xml .= "        <chat_time>10000</chat_time>\n";
        $xml .= "        <finishtimeout>1</finishtimeout>\n";
        $xml .= "        <allwarmup_duration>0</allwarmup_duration>\n";
        $xml .= "        <disablerespawn>0</disablerespawn>\n";
        $xml .= "        <forceshowallopponents>0</forceshowallopponents>\n";
        if ($scriptName !== '') {
            $xml .= "        <script_name>" . htmlspecialchars($scriptName, ENT_XML1, 'UTF-8') . "</script_name>\n";
        }
        $xml .= "    </gameinfos>\n";
        $xml .= "    <hotseat>\n";
        $xml .= "        <game_mode>1</game_mode>\n";
        $xml .= "        <time_limit>300000</time_limit>\n";
        $xml .= "        <rounds_count>5</rounds_count>\n";
        $xml .= "    </hotseat>\n";
        $xml .= "    <filter>\n";
        $xml .= "        <is_lan>1</is_lan>\n";
        $xml .= "        <is_internet>1</is_internet>\n";
        $xml .= "    </filter>\n";

        foreach ($mapFiles as $mapPath) {
            $cleanMap = htmlspecialchars(trim((string)$mapPath), ENT_XML1, 'UTF-8');
            $xml .= "    <map>\n";
            $xml .= "        <file>{$cleanMap}</file>\n";
            $xml .= "    </map>\n";
        }

        $xml .= "</playlist>\n";

        if (file_put_contents($fullPath, $xml) === false) {
            throw new RuntimeException("Could not write MatchSettings file to {$fullPath}");
        }

        return 'MatchSettings/' . $cleanName;
    }
}
