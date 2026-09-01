<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;
use App\Services\TmNickParser;
use Throwable;

class DashboardController
{
    private AuthManager $auth;
    private ServerManager $serverManager;
    private ?Logger $logger;

    public function __construct(AuthManager $auth, ServerManager $serverManager, ?Logger $logger = null)
    {
        $this->auth = $auth;
        $this->serverManager = $serverManager;
        $this->logger = $logger;
    }

    private function getActiveClient()
    {
        $serverName = $this->auth->getActiveServer();
        if (!$serverName) {
            $servers = $this->serverManager->getServers();
            $serverName = $servers[0]['name'] ?? '';
        }
        $user = $this->auth->getUser();
        return $this->serverManager->getClient($serverName, $user['level'] ?? 'User');
    }

    public function overview(): array
    {
        $servers = $this->serverManager->getServers();
        $overview = [];

        foreach ($servers as $s) {
            $name = $s['name'];
            $addr = $s['address'] ?? '127.0.0.1';
            $port = (int)($s['port'] ?? 5000);
            $timeout = (float)($s['timeout'] ?? 1.5);

            try {
                $client = new \App\Services\Gbx\GbxRemoteClient($addr, $port, min($timeout, 1.5));
                $client->connect();
                $pw = $s['admin_passwords']['User'] ?? ($s['admin_passwords']['Admin'] ?? '');
                if ($pw !== '') {
                    try {
                        $client->authenticate('User', $pw);
                    } catch (Throwable) {
                        // ignore auth error for overview
                    }
                }

                $version = null;
                try {
                    $version = $client->query('GetVersion');
                } catch (Throwable) {}

                $serverOptions = null;
                try {
                    $serverOptions = $client->query('GetServerOptions');
                } catch (Throwable) {}

                $currentMap = null;
                try {
                    $currentMap = $client->queryCompat('GetCurrentMapInfo', 'GetCurrentChallengeInfo');
                } catch (Throwable) {}

                $players = [];
                try {
                    $players = $client->query('GetPlayerList', 300, 0);
                } catch (Throwable) {}

                $rawName = $serverOptions['Name'] ?? $name;
                $rawMap = $currentMap['Name'] ?? 'No Map';

                $overview[] = [
                    'name' => $name,
                    'address' => $addr,
                    'port' => $port,
                    'connected' => true,
                    'server_name' => $rawName,
                    'server_title' => TmNickParser::toHtml($rawName),
                    'version' => $version['Name'] ?? ($version['Version'] ?? 'Trackmania'),
                    'players_count' => is_array($players) ? count($players) : 0,
                    'max_players' => (int)($serverOptions['CurrentMaxPlayers'] ?? 32),
                    'current_map' => [
                        'name_raw' => $rawMap,
                        'name_html' => TmNickParser::toHtml($rawMap),
                        'name_clean' => TmNickParser::strip($rawMap),
                        'author' => $currentMap['Author'] ?? '',
                        'environment' => $currentMap['Environnement'] ?? '',
                    ],
                ];
            } catch (Throwable $e) {
                $overview[] = [
                    'name' => $name,
                    'address' => $addr,
                    'port' => $port,
                    'connected' => false,
                    'error' => $e->getMessage(),
                    'server_name' => $name,
                    'server_title' => htmlspecialchars($name),
                    'players_count' => 0,
                    'max_players' => 0,
                    'current_map' => null,
                ];
            }
        }

        return [
            'success' => true,
            'overview' => $overview,
        ];
    }

    public function status(): array
    {
        try {
            $serverName = $this->auth->getActiveServer();
            if (!$serverName) {
                $servers = $this->serverManager->getServers();
                $serverName = $servers[0]['name'] ?? '';
            }

            $user = $this->auth->getUser();
            $client = $this->serverManager->getClient($serverName, $user['level'] ?? 'User');
            $serverConfig = $this->serverManager->getServerConfig($serverName);

            $version = $client->query('GetVersion');
            $serverOptions = $client->query('GetServerOptions');
            $currentMap = $client->queryCompat('GetCurrentMapInfo', 'GetCurrentChallengeInfo');
            $nextMap = $client->queryCompat('GetNextMapInfo', 'GetNextChallengeInfo');
            $playerList = $client->query('GetPlayerList', 300, 0);

            $ladderLimits = [];
            try {
                $ladderLimits = $client->query('GetLadderServerLimits');
            } catch (Throwable) {
                // Ladder limits not supported on all game builds
            }

            // Format current map info
            $mapNameRaw = $currentMap['Name'] ?? 'Unknown Map';
            $formattedMapName = TmNickParser::toHtml($mapNameRaw);
            $strippedMapName = TmNickParser::strip($mapNameRaw);

            // Separate players and spectators
            $playersCount = 0;
            $spectatorsCount = 0;
            if (is_array($playerList)) {
                foreach ($playerList as $p) {
                    if (!empty($p['IsSpectator'])) {
                        $spectatorsCount++;
                    } else {
                        $playersCount++;
                    }
                }
            }

            return [
                'success' => true,
                'connected' => true,
                'server_name' => $serverName,
                'server_title' => TmNickParser::toHtml($serverOptions['Name'] ?? $serverName),
                'server_comment' => $serverOptions['Comment'] ?? '',
                'version' => $version['Version'] ?? 'N/A',
                'build' => $version['Build'] ?? 'N/A',
                'current_map' => [
                    'uid' => $currentMap['UId'] ?? '',
                    'name_raw' => $mapNameRaw,
                    'name_html' => $formattedMapName,
                    'name_clean' => $strippedMapName,
                    'author' => $currentMap['Author'] ?? 'Unknown',
                    'environment' => $currentMap['Environnement'] ?? '',
                    'mood' => $currentMap['Mood'] ?? '',
                    'author_time' => $currentMap['AuthorTime'] ?? 0,
                    'gold_time' => $currentMap['GoldTime'] ?? 0,
                    'silver_time' => $currentMap['SilverTime'] ?? 0,
                    'bronze_time' => $currentMap['BronzeTime'] ?? 0,
                    'file_name' => $currentMap['FileName'] ?? '',
                ],
                'next_map' => [
                    'uid' => $nextMap['UId'] ?? '',
                    'name_html' => TmNickParser::toHtml($nextMap['Name'] ?? ''),
                    'name_clean' => TmNickParser::strip($nextMap['Name'] ?? ''),
                    'author' => $nextMap['Author'] ?? '',
                    'environment' => $nextMap['Environnement'] ?? '',
                ],
                'players_count' => $playersCount,
                'spectators_count' => $spectatorsCount,
                'max_players' => $serverOptions['CurrentMaxPlayers'] ?? 32,
                'max_spectators' => $serverOptions['CurrentMaxSpectators'] ?? 32,
                'ladder_limits' => $ladderLimits,
                'is_password_protected' => !empty($serverOptions['CurrentPlayerPassword']),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'connected' => false,
                'error' => $e->getMessage(),
                'server_name' => $this->auth->getActiveServer() ?? 'Disconnected',
            ];
        }
    }

    public function chat(array $params = []): array
    {
        try {
            $client = $this->getActiveClient();
            $lines = $client->query('GetChatLines');
            $chatHistory = [];

            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $chatHistory[] = [
                        'raw' => $line,
                        'html' => TmNickParser::toHtml($line),
                        'clean' => TmNickParser::strip($line),
                    ];
                }
            }

            return ['success' => true, 'messages' => $chatHistory];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'messages' => []];
        }
    }

    public function sendChat(array $data): array
    {
        if (!$this->auth->hasPermission('chat_sendmessage')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $message = trim((string)($data['message'] ?? ''));
        if ($message === '') {
            return ['success' => false, 'error' => 'Message cannot be empty.'];
        }

        try {
            $client = $this->getActiveClient();
            $client->query('ChatSendServerMessage', $message);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Broadcast server message: \"{$message}\"", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to broadcast server message: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function action(array $data): array
    {
        $action = (string)($data['action'] ?? '');
        $client = $this->getActiveClient();

        try {
            switch ($action) {
                case 'restart':
                    if (!$this->auth->hasPermission('speed_admin')) return ['success' => false, 'error' => 'Permission denied.'];
                    $client->queryCompat('RestartMap', 'RestartChallenge');
                    break;
                case 'next':
                    if (!$this->auth->hasPermission('speed_admin')) return ['success' => false, 'error' => 'Permission denied.'];
                    $client->queryCompat('NextMap', 'NextChallenge');
                    break;
                case 'cancel_vote':
                    if (!$this->auth->hasPermission('cancel_vote')) return ['success' => false, 'error' => 'Permission denied.'];
                    $client->query('CancelVote');
                    break;
                default:
                    return ['success' => false, 'error' => "Unknown action: {$action}"];
            }

            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Speed action '{$action}' executed", $server, $user['level'] ?? null);

            return ['success' => true, 'action' => $action];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Speed action '{$action}' failed: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
