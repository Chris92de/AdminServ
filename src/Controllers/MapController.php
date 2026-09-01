<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Gbx\GbxFetcher;
use App\Services\Logger;
use App\Services\MapBrowser;
use App\Services\ServerManager;
use App\Services\TmNickParser;
use Throwable;

class MapController
{
    private AuthManager $auth;
    private ServerManager $serverManager;
    private array $config;
    private ?Logger $logger;

    public function __construct(AuthManager $auth, ServerManager $serverManager, array $config, ?Logger $logger = null)
    {
        $this->auth = $auth;
        $this->serverManager = $serverManager;
        $this->config = $config;
        $this->logger = $logger;
    }

    private function getClient()
    {
        $serverName = $this->auth->getActiveServer();
        if (!$serverName) {
            $servers = $this->serverManager->getServers();
            $serverName = $servers[0]['name'] ?? '';
        }
        $user = $this->auth->getUser();
        return $this->serverManager->getClient($serverName, $user['level'] ?? 'User');
    }

    private function getMapBrowser(?string $serverName = null): MapBrowser
    {
        if (!$serverName) {
            $serverName = $this->auth->getActiveServer();
        }
        if (!$serverName) {
            $servers = $this->serverManager->getServers();
            $serverName = $servers[0]['name'] ?? '';
        }
        $srv = $this->serverManager->getServerConfig($serverName ?? '');
        $basePath = $srv['maps_base_path'] ?? '';
        $allowedExts = $this->config['maps']['allowed_extensions'] ?? ['gbx', 'map.gbx', 'challenge.gbx'];
        $hiddenFolders = $this->config['maps']['hidden_folders'] ?? ['Campaigns', 'Replays'];

        return new MapBrowser($basePath, $allowedExts, $hiddenFolders);
    }

    public function playlist(): array
    {
        try {
            $client = $this->getClient();
            $list = $client->queryCompat('GetMapList', 'GetChallengeList', 1000, 0);
            $currentMap = $client->queryCompat('GetCurrentMapInfo', 'GetCurrentChallengeInfo');
            $currentUid = $currentMap['UId'] ?? '';

            if (!isset($_SESSION['adminserv_map_details']) || !is_array($_SESSION['adminserv_map_details'])) {
                $_SESSION['adminserv_map_details'] = [];
            }

            // Cache current map info
            if ($currentUid !== '' && isset($currentMap['AuthorTime'])) {
                $_SESSION['adminserv_map_details'][$currentUid] = [
                    'author_time' => (int)$currentMap['AuthorTime'],
                    'author_nickname' => $currentMap['AuthorNickname'] ?? ($currentMap['Author'] ?? ''),
                ];
            }

            $playlist = [];
            if (is_array($list)) {
                foreach ($list as $index => $m) {
                    $rawName = $m['Name'] ?? 'Unknown';
                    $uid = $m['UId'] ?? '';
                    $fileName = $m['FileName'] ?? '';
                    $authorTime = (int)($m['AuthorTime'] ?? 0);
                    $author = $m['Author'] ?? 'Unknown';

                    // If AuthorTime is not in list struct, check cache or fetch via GetMapInfo
                    if ($authorTime === 0 && $uid !== '') {
                        if (isset($_SESSION['adminserv_map_details'][$uid])) {
                            $authorTime = $_SESSION['adminserv_map_details'][$uid]['author_time'] ?? 0;
                            $cachedNick = $_SESSION['adminserv_map_details'][$uid]['author_nickname'] ?? '';
                            if ($cachedNick !== '') {
                                $author = $cachedNick;
                            }
                        } elseif ($fileName !== '') {
                            try {
                                $info = $client->queryCompat('GetMapInfo', 'GetChallengeInfo', $fileName);
                                $authorTime = (int)($info['AuthorTime'] ?? 0);
                                $authorNick = $info['AuthorNickname'] ?? ($info['Author'] ?? $author);
                                if ($authorNick !== '') {
                                    $author = $authorNick;
                                }
                                $_SESSION['adminserv_map_details'][$uid] = [
                                    'author_time' => $authorTime,
                                    'author_nickname' => $author,
                                ];
                            } catch (Throwable) {
                                // Continue if individual map query fails
                            }
                        }
                    }

                    $playlist[] = [
                        'index' => $index,
                        'uid' => $uid,
                        'name_raw' => $rawName,
                        'name_html' => TmNickParser::toHtml($rawName),
                        'name_clean' => TmNickParser::strip($rawName),
                        'author' => $author,
                        'environment' => $m['Environnement'] ?? '',
                        'file_name' => $fileName,
                        'gold_time' => (int)($m['GoldTime'] ?? 0),
                        'author_time' => $authorTime,
                        'is_current' => ($uid === $currentUid),
                    ];
                }
            }

            return ['success' => true, 'playlist' => $playlist];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'playlist' => []];
        }
    }

    public function remove(array $data): array
    {
        if (!$this->auth->hasPermission('maps_manage')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $fileName = (string)($data['file_name'] ?? '');
        if ($fileName === '') {
            return ['success' => false, 'error' => 'Filename required.'];
        }

        try {
            $client = $this->getClient();
            $client->queryCompat('RemoveMap', 'RemoveChallenge', $fileName);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Removed map '{$fileName}' from playlist", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to remove map '{$fileName}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function insert(array $data): array
    {
        if (!$this->auth->hasPermission('maps_manage')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $fileName = (string)($data['file_name'] ?? '');
        if ($fileName === '') {
            return ['success' => false, 'error' => 'Filename required.'];
        }

        try {
            $client = $this->getClient();
            $client->queryCompat('InsertMap', 'InsertChallenge', $fileName);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Added map '{$fileName}' to playlist", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to add map '{$fileName}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function jump(array $data): array
    {
        if (!$this->auth->hasPermission('speed_admin')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $index = (int)($data['index'] ?? 0);

        try {
            $client = $this->getClient();
            $client->queryCompat('SetNextMapIndex', 'SetNextChallengeIndex', $index);
            $client->queryCompat('NextMap', 'NextChallenge');
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Jumped to map index #{$index}", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to jump to map index #{$index}: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function local(array $params = []): array
    {
        try {
            $browser = $this->getMapBrowser();
            $path = (string)($params['path'] ?? '');
            return ['success' => true, 'data' => $browser->listDirectory($path)];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to list local maps in '{$params['path']}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function upload(array $files, array $data): array
    {
        if (!$this->auth->hasPermission('maps_upload')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        if (empty($files['map_file'])) {
            return ['success' => false, 'error' => 'No map file was uploaded.'];
        }

        $subDir = (string)($data['sub_dir'] ?? '');

        try {
            $browser = $this->getMapBrowser();
            $result = $browser->saveUpload($files['map_file'], $subDir);
            if (!empty($result['success'])) {
                $user = $this->auth->getUser();
                $server = $this->auth->getActiveServer();
                $uploadedName = $result['filename'] ?? 'map';
                $this->logger?->action("Uploaded map file '{$uploadedName}' to path '{$subDir}'", $server, $user['level'] ?? null);
            } else if (!empty($result['error'])) {
                $user = $this->auth->getUser();
                $server = $this->auth->getActiveServer();
                $this->logger?->error("Failed to upload map: {$result['error']}", $server, $user['level'] ?? null);
            }
            return $result;
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to upload map: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function thumbnail(array $params): void
    {
        $filePath = (string)($params['path'] ?? ($params['file'] ?? ''));
        if ($filePath === '') {
            header('HTTP/1.1 400 Bad Request');
            exit;
        }

        $serverName = !empty($params['server']) ? (string)$params['server'] : null;
        $browser = $this->getMapBrowser($serverName);
        $resolved = $browser->resolvePath($filePath);

        // If not found in current/specified server path, search across all servers
        if (!file_exists($resolved) || !is_file($resolved)) {
            $servers = $this->serverManager->getServers();
            foreach ($servers as $s) {
                $b = new MapBrowser($s['maps_base_path'] ?? '');
                $candidate = $b->resolvePath($filePath);
                if (file_exists($candidate) && is_file($candidate)) {
                    $resolved = $candidate;
                    break;
                }
            }
        }

        if (file_exists($resolved) && is_file($resolved)) {
            $gbx = new GbxFetcher($resolved);
            $thumb = $gbx->getThumbnail();
            if ($thumb !== null && $thumb !== '') {
                header('Content-Type: image/jpeg');
                header('Cache-Control: public, max-age=86400');
                echo $thumb;
                exit;
            }
        }

        // Return 404
        header('HTTP/1.1 404 Not Found');
        exit;
    }
}
