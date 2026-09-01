<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;

class AuthController
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

    public function me(): array
    {
        $user = $this->auth->getUser();
        $servers = $this->serverManager->getServers();
        $activeServer = $this->auth->getActiveServer() ?? ($servers[0]['name'] ?? null);

        return [
            'authenticated' => $this->auth->isLoggedIn(),
            'user' => $user,
            'active_server' => $activeServer,
            'servers' => $servers,
            'app_info' => [
                'title' => $this->config['app']['title'] ?? 'AdminServ',
                'subtitle' => $this->config['app']['subtitle'] ?? '',
                'version' => \App\App::VERSION,
                'default_theme' => $this->config['app']['default_theme'] ?? 'dark',
            ],
        ];
    }

    public function login(array $data): array
    {
        $serverName = (string)($data['server'] ?? '');
        $password = (string)($data['password'] ?? '');
        $level = !empty($data['level']) ? (string)$data['level'] : null;

        if ($serverName === '' || $password === '') {
            return ['success' => false, 'error' => 'Server and password are required.'];
        }

        $success = $this->auth->login($serverName, $password, $level);
        if (!$success) {
            $this->logger?->warning("Failed login attempt for level '{$level}'", $serverName);
            return ['success' => false, 'error' => 'Invalid password for selected user level.'];
        }

        $user = $this->auth->getUser();
        $this->logger?->info("Logged in successfully as '{$user['level']}'", $serverName, $user['level']);

        return [
            'success' => true,
            'user' => $user,
            'active_server' => $serverName,
        ];
    }

    public function logout(): array
    {
        $user = $this->auth->getUser();
        $server = $this->auth->getActiveServer();
        $this->logger?->info("Logged out", $server, $user['level'] ?? null);
        $this->auth->logout();
        return ['success' => true];
    }

    public function switchServer(array $data): array
    {
        $serverName = (string)($data['server'] ?? '');
        $password = !empty($data['password']) ? (string)$data['password'] : null;
        $level = !empty($data['level']) ? (string)$data['level'] : null;

        $servers = $this->serverManager->getServers();
        $exists = false;
        foreach ($servers as $s) {
            if ($s['name'] === $serverName) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            return ['success' => false, 'error' => "Server '{$serverName}' does not exist."];
        }

        // If currently logged in, switching to another server requires credentials
        if ($this->auth->isLoggedIn()) {
            if ($password === null) {
                // Terminate session for previous server and switch preview context
                $this->auth->logout();
                $this->auth->setActiveServer($serverName);
                return [
                    'success' => true,
                    'authenticated' => false,
                    'active_server' => $serverName,
                    'require_auth' => true,
                ];
            }

            // Authenticate directly against target server
            $success = $this->auth->login($serverName, $password, $level);
            if (!$success) {
                return ['success' => false, 'error' => 'Invalid password for selected server and user level.'];
            }

            return [
                'success' => true,
                'authenticated' => true,
                'user' => $this->auth->getUser(),
                'active_server' => $serverName,
            ];
        }

        // When unauthenticated, switching server updates preview context
        $this->auth->setActiveServer($serverName);
        return [
            'success' => true,
            'authenticated' => false,
            'active_server' => $serverName,
        ];
    }
}
