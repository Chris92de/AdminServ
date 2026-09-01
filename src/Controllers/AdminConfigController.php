<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;
use Throwable;

class AdminConfigController
{
    private AuthManager $auth;
    private ServerManager $serverManager;
    private array $config;
    private string $configFile;
    private ?Logger $logger;

    public function __construct(AuthManager $auth, ServerManager $serverManager, array $config, ?string $configFile = null, ?Logger $logger = null)
    {
        $this->auth = $auth;
        $this->serverManager = $serverManager;
        $this->config = $config;
        $this->configFile = $configFile ?? (dirname(__DIR__, 2) . '/config/adminserv.php');
        $this->logger = $logger;
    }

    public function status(): array
    {
        return [
            'success' => true,
            'enabled' => (bool)($this->config['master_config']['enabled'] ?? true),
            'unlocked' => $this->auth->isConfigUnlocked(),
        ];
    }

    public function unlock(array $data): array
    {
        $password = (string)($data['password'] ?? '');
        if ($this->auth->unlockConfig($password)) {
            $this->logger?->info("Master Configuration unlocked");
            return ['success' => true, 'unlocked' => true];
        }
        $this->logger?->warning("Failed Master Configuration unlock attempt");
        return ['success' => false, 'error' => 'Invalid master configuration password.'];
    }

    public function lock(): array
    {
        $this->auth->lockConfig();
        return ['success' => true, 'unlocked' => false];
    }

    public function getServers(): array
    {
        return [
            'success' => true,
            'servers' => $this->serverManager->getServers(),
        ];
    }

    public function getAdminLevels(): array
    {
        return [
            'success' => true,
            'admin_levels' => $this->config['admin_levels'] ?? [],
        ];
    }

    public function saveServer(array $data): array
    {
        if (!$this->auth->isConfigUnlocked()) {
            return ['success' => false, 'error' => 'Configuration is locked. Master password required.'];
        }

        $originalName = (string)($data['original_name'] ?? '');
        $serverData = (array)($data['server'] ?? []);

        try {
            $this->serverManager->saveServer($originalName, $serverData, $this->configFile);
            $targetName = $serverData['name'] ?? $originalName;
            $this->logger?->action("Saved server configuration for '{$targetName}'");
            return [
                'success' => true,
                'servers' => $this->serverManager->getServers(),
            ];
        } catch (Throwable $e) {
            $this->logger?->error("Failed to save server configuration: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteServer(array $data): array
    {
        if (!$this->auth->isConfigUnlocked()) {
            return ['success' => false, 'error' => 'Configuration is locked. Master password required.'];
        }

        $name = (string)($data['name'] ?? '');
        if ($name === '') {
            return ['success' => false, 'error' => 'Server name is required.'];
        }

        try {
            $this->serverManager->deleteServer($name, $this->configFile);
            $this->logger?->action("Deleted server configuration '{$name}'");
            return [
                'success' => true,
                'servers' => $this->serverManager->getServers(),
            ];
        } catch (Throwable $e) {
            $this->logger?->error("Failed to delete server configuration '{$name}': " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function testServer(array $data): array
    {
        $host = (string)($data['address'] ?? '127.0.0.1');
        $port = (int)($data['port'] ?? 5000);
        $timeout = (float)($data['timeout'] ?? 3.0);
        $login = !empty($data['login']) ? (string)$data['login'] : null;
        $password = !empty($data['password']) ? (string)$data['password'] : null;

        return $this->serverManager->testConnection($host, $port, $timeout, $login, $password);
    }

    public function changePassword(array $data): array
    {
        if (!$this->auth->isConfigUnlocked()) {
            return ['success' => false, 'error' => 'Configuration is locked. Master password required.'];
        }

        $newPassword = (string)($data['new_password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');

        if ($newPassword === '') {
            return ['success' => false, 'error' => 'New master password cannot be empty.'];
        }

        if (strlen($newPassword) < 4) {
            return ['success' => false, 'error' => 'Master password must be at least 4 characters long.'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'error' => 'Password confirmation does not match.'];
        }

        try {
            $this->serverManager->updateMasterPassword($newPassword, $this->configFile);
            $this->logger?->action("Changed Master Configuration password");
            return [
                'success' => true,
                'message' => 'Master configuration password updated successfully!',
            ];
        } catch (Throwable $e) {
            $this->logger?->error("Failed to change master password: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
