<?php
declare(strict_types=1);

namespace App\Services;

class AuthManager
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['adminserv_user']);
    }

    public function getUser(): ?array
    {
        return $_SESSION['adminserv_user'] ?? null;
    }

    public function getActiveServer(): ?string
    {
        return $_SESSION['adminserv_active_server'] ?? null;
    }

    public function setActiveServer(string $serverName): void
    {
        $_SESSION['adminserv_active_server'] = $serverName;
    }

    public function login(string $serverName, string $password, ?string $requestedLevel = null): bool
    {
        $servers = $this->config['servers'] ?? [];
        if (!isset($servers[$serverName])) {
            return false;
        }

        $server = $servers[$serverName];
        $passwords = $server['admin_passwords'] ?? [];

        $matchedLevel = null;
        if ($requestedLevel !== null && $requestedLevel !== '') {
            $expectedPw = $passwords[$requestedLevel] ?? null;
            if ($expectedPw !== null && ($password === $expectedPw || password_verify($password, $expectedPw))) {
                $matchedLevel = $requestedLevel;
            }
        } else {
            // Check password against defined admin levels
            foreach ($passwords as $level => $pw) {
                if ($password === $pw || password_verify($password, $pw)) {
                    $matchedLevel = $level;
                    break;
                }
            }
        }

        if (!$matchedLevel) {
            return false;
        }

        $levelConfig = $this->config['admin_levels'][$matchedLevel] ?? [
            'name' => $matchedLevel,
            'permissions' => []
        ];

        $_SESSION['adminserv_user'] = [
            'level' => $matchedLevel,
            'name' => $matchedLevel,
            'permissions' => $levelConfig['permissions'] ?? [],
            'logged_at' => time(),
        ];
        $_SESSION['adminserv_active_server'] = $serverName;

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['adminserv_user']);
        unset($_SESSION['adminserv_active_server']);
        unset($_SESSION['adminserv_config_unlocked']);
    }

    public function hasPermission(string $permission): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        if ($user['level'] === 'SuperAdmin') {
            return true;
        }

        return in_array($permission, $user['permissions'] ?? [], true);
    }

    public function isConfigUnlocked(): bool
    {
        $enabled = $this->config['master_config']['enabled'] ?? true;
        if (!$enabled) {
            return false;
        }
        return !empty($_SESSION['adminserv_config_unlocked']);
    }

    public function unlockConfig(string $password): bool
    {
        $masterPw = (string)($this->config['master_config']['password'] ?? '');
        if ($masterPw === '') {
            return false;
        }

        // Support bcrypt hashes as primary, with fallback for plain text / MD5
        if (password_verify($password, $masterPw) || $password === $masterPw || md5($password) === $masterPw) {
            $_SESSION['adminserv_config_unlocked'] = true;
            return true;
        }

        return false;
    }

    public function lockConfig(): void
    {
        unset($_SESSION['adminserv_config_unlocked']);
    }
}
