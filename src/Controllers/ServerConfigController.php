<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;
use Throwable;

class ServerConfigController
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
        $user = $this->auth->getUser();
        return $this->serverManager->getClient($serverName ?? '', $user['level'] ?? 'User');
    }

    public function getOptions(): array
    {
        try {
            $client = $this->getClient();
            $options = $client->query('GetServerOptions');
            return ['success' => true, 'options' => $options];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function setOptions(array $data): array
    {
        if (!$this->auth->hasPermission('srvopts_general_name')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        try {
            $client = $this->getClient();
            $current = $client->query('GetServerOptions');

            $newOptions = array_merge($current, [
                'Name' => (string)($data['Name'] ?? $current['Name']),
                'Comment' => (string)($data['Comment'] ?? $current['Comment']),
                'Password' => (string)($data['Password'] ?? $current['Password']),
                'PasswordForSpectator' => (string)($data['PasswordForSpectator'] ?? $current['PasswordForSpectator']),
                'CurrentMaxPlayers' => (int)($data['CurrentMaxPlayers'] ?? $current['CurrentMaxPlayers']),
                'CurrentMaxSpectators' => (int)($data['CurrentMaxSpectators'] ?? $current['CurrentMaxSpectators']),
            ]);

            $client->query('SetServerOptions', $newOptions);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Updated server options (Name: '{$newOptions['Name']}', MaxPlayers: {$newOptions['CurrentMaxPlayers']})", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to set server options: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getGameInfos(): array
    {
        try {
            $client = $this->getClient();
            $gameInfos = $client->query('GetCurrentGameInfo');
            return ['success' => true, 'game_infos' => $gameInfos];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function setGameInfos(array $data): array
    {
        if (!$this->auth->hasPermission('gameinfos_gamemode')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        try {
            $client = $this->getClient();
            $current = $client->query('GetCurrentGameInfo');

            $updated = array_merge($current, [
                'GameMode' => (int)($data['GameMode'] ?? $current['GameMode']),
                'ChatTime' => (int)($data['ChatTime'] ?? $current['ChatTime']),
                'FinishTimeout' => (int)($data['FinishTimeout'] ?? $current['FinishTimeout']),
                'AllWarmUpDuration' => (int)($data['AllWarmUpDuration'] ?? $current['AllWarmUpDuration']),
                'DisableRespawn' => (bool)($data['DisableRespawn'] ?? $current['DisableRespawn']),
                'ForceShowAllOpponents' => (int)($data['ForceShowAllOpponents'] ?? $current['ForceShowAllOpponents']),
            ]);

            // Mode specific parameters
            if (isset($data['RoundsPointsLimit'])) $updated['RoundsPointsLimit'] = (int)$data['RoundsPointsLimit'];
            if (isset($data['TimeAttackLimit'])) $updated['TimeAttackLimit'] = (int)$data['TimeAttackLimit'];
            if (isset($data['TeamPointsLimit'])) $updated['TeamPointsLimit'] = (int)$data['TeamPointsLimit'];
            if (isset($data['LapsNbLaps'])) $updated['LapsNbLaps'] = (int)$data['LapsNbLaps'];
            if (isset($data['CupPointsLimit'])) $updated['CupPointsLimit'] = (int)$data['CupPointsLimit'];

            $client->query('SetGameInfo', $updated);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Updated game mode settings (GameMode: {$updated['GameMode']})", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to set game mode settings: " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getScriptSettings(): array
    {
        try {
            $client = $this->getClient();
            $info = [];
            $settings = [];

            try {
                $info = $client->query('GetModeScriptInfo');
            } catch (Throwable) {
                // Not in script mode
            }

            try {
                $settings = $client->query('GetModeScriptSettings');
            } catch (Throwable) {
                // Not in script mode
            }

            return [
                'success' => true,
                'is_script_mode' => !empty($settings),
                'info' => $info,
                'settings' => $settings,
            ];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function setScriptSettings(array $data): array
    {
        if (!$this->auth->hasPermission('gameinfos_gamemode')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $settings = (array)($data['settings'] ?? []);
        if (empty($settings)) {
            return ['success' => false, 'error' => 'No settings provided.'];
        }

        try {
            $client = $this->getClient();
            $client->query('SetModeScriptSettings', $settings);
            return ['success' => true];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
