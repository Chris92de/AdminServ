<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;
use App\Services\TmNickParser;
use Throwable;

class PlayerController
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

    public function index(): array
    {
        try {
            $client = $this->getClient();
            $rawPlayers = $client->query('GetPlayerList', 300, 0);

            $detailedList = [];
            if (is_array($rawPlayers)) {
                foreach ($rawPlayers as $p) {
                    $login = $p['Login'] ?? '';
                    $nickRaw = $p['NickName'] ?? $login;

                    $detailedList[] = [
                        'login' => $login,
                        'nickname_raw' => $nickRaw,
                        'nickname_html' => TmNickParser::toHtml($nickRaw),
                        'nickname_clean' => TmNickParser::strip($nickRaw),
                        'player_id' => $p['PlayerId'] ?? 0,
                        'team_id' => $p['TeamId'] ?? -1,
                        'is_spectator' => !empty($p['IsSpectator']),
                        'is_in_official_mode' => !empty($p['IsInOfficialMode']),
                        'ladder_ranking' => $p['LadderRanking'] ?? 0,
                        'flags' => $p['Flags'] ?? 0,
                    ];
                }
            }

            return ['success' => true, 'players' => $detailedList];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'players' => []];
        }
    }

    public function kick(array $data): array
    {
        if (!$this->auth->hasPermission('player_kick')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $login = (string)($data['login'] ?? '');
        $msg = (string)($data['message'] ?? 'Kicked by administrator');

        try {
            $client = $this->getClient();
            $client->query('Kick', $login, $msg);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Kicked player '{$login}' (Reason: {$msg})", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to kick player '{$login}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function ban(array $data): array
    {
        if (!$this->auth->hasPermission('player_ban')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $login = (string)($data['login'] ?? '');
        $msg = (string)($data['message'] ?? 'Banned by administrator');

        try {
            $client = $this->getClient();
            $client->query('Ban', $login, $msg);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Banned player '{$login}' (Reason: {$msg})", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to ban player '{$login}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function unban(array $data): array
    {
        if (!$this->auth->hasPermission('player_ban')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $login = (string)($data['login'] ?? '');

        try {
            $client = $this->getClient();
            $client->query('UnBan', $login);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Unbanned player '{$login}'", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to unban player '{$login}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function forceSpectator(array $data): array
    {
        if (!$this->auth->hasPermission('player_forcetospectator')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $login = (string)($data['login'] ?? '');
        $cameraType = (int)($data['camera_type'] ?? 1);

        try {
            $client = $this->getClient();
            $client->query('ForceSpectator', $login, $cameraType);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Forced player '{$login}' to spectator", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to force spectator for '{$login}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function forcePlayer(array $data): array
    {
        if (!$this->auth->hasPermission('player_forcetoplayer')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $login = (string)($data['login'] ?? '');

        try {
            $client = $this->getClient();
            $client->query('ForcePlayer', $login);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Forced player '{$login}' to player mode", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to force player mode for '{$login}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function bans(): array
    {
        try {
            $client = $this->getClient();
            $banList = $client->query('GetBanList', 300, 0);
            $blackList = $client->query('GetBlackList', 300, 0);
            $guestList = $client->query('GetGuestList', 300, 0);

            return [
                'success' => true,
                'bans' => $banList ?: [],
                'blacklisted' => $blackList ?: [],
                'guests' => $guestList ?: [],
            ];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
