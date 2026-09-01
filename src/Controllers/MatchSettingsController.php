<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\MatchSettingsManager;
use App\Services\ServerManager;
use Throwable;

class MatchSettingsController
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

    private function getManager(): MatchSettingsManager
    {
        $serverName = $this->auth->getActiveServer();
        $srv = $this->serverManager->getServerConfig($serverName ?? '');
        $basePath = $srv['maps_base_path'] ?? '';
        return new MatchSettingsManager($basePath);
    }

    private function getClient()
    {
        $serverName = $this->auth->getActiveServer();
        $user = $this->auth->getUser();
        return $this->serverManager->getClient($serverName ?? '', $user['level'] ?? 'User');
    }

    public function index(): array
    {
        try {
            $manager = $this->getManager();
            $files = $manager->listFiles();
            return ['success' => true, 'files' => $files];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'files' => []];
        }
    }

    public function detail(array $params): array
    {
        $path = (string)($params['path'] ?? '');
        try {
            $manager = $this->getManager();
            $detail = $manager->parseFile($path);
            return ['success' => true, 'data' => $detail];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function load(array $data): array
    {
        if (!$this->auth->hasPermission('maps_matchsettings')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $path = (string)($data['path'] ?? '');
        if ($path === '') {
            return ['success' => false, 'error' => 'MatchSettings path required.'];
        }

        try {
            $client = $this->getClient();
            $client->query('LoadMatchSettings', $path);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Loaded MatchSettings playlist '{$path}'", $server, $user['level'] ?? null);
            return ['success' => true];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to load MatchSettings '{$path}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function create(array $data): array
    {
        if (!$this->auth->hasPermission('maps_matchsettings')) {
            return ['success' => false, 'error' => 'Permission denied.'];
        }

        $filename = (string)($data['filename'] ?? '');
        $maps = (array)($data['maps'] ?? []);
        $gameMode = (int)($data['game_mode'] ?? 1);
        $scriptName = (string)($data['script_name'] ?? '');

        if ($filename === '' || empty($maps)) {
            return ['success' => false, 'error' => 'Filename and at least one map required.'];
        }

        try {
            $manager = $this->getManager();
            $savedPath = $manager->saveMatchSettings($filename, $maps, $gameMode, $scriptName);
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->action("Created MatchSettings playlist '{$filename}' with " . count($maps) . " maps", $server, $user['level'] ?? null);
            return ['success' => true, 'path' => $savedPath];
        } catch (Throwable $e) {
            $user = $this->auth->getUser();
            $server = $this->auth->getActiveServer();
            $this->logger?->error("Failed to create MatchSettings '{$filename}': " . $e->getMessage(), $server, $user['level'] ?? null);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
