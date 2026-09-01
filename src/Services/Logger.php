<?php
declare(strict_types=1);

namespace App\Services;

class Logger
{
    private bool $enabled;
    private string $logPath;

    public function __construct(array $config)
    {
        $this->enabled = (bool)($config['logs']['enabled'] ?? true);
        $rawPath = $config['logs']['path'] ?? (dirname(__DIR__, 2) . '/logs/adminserv.log');
        $this->logPath = (string)$rawPath;
    }

    public function log(string $level, string $message, ?string $serverName = null, ?string $userName = null): void
    {
        if (!$this->enabled || $this->logPath === '') {
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userTag = ($userName ?? 'Guest') . '@' . $ip;
        $serverTag = ($serverName !== null && $serverName !== '') ? "[{$serverName}] " : '';
        $time = date('Y-m-d H:i:s');
        $formatted = sprintf("[%s] [%s] [%s] %s%s\n", $time, strtoupper($level), $userTag, $serverTag, $message);

        $dir = dirname($this->logPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        @file_put_contents($this->logPath, $formatted, FILE_APPEND | LOCK_EX);
    }

    public function info(string $message, ?string $serverName = null, ?string $userName = null): void
    {
        $this->log('INFO', $message, $serverName, $userName);
    }

    public function action(string $message, ?string $serverName = null, ?string $userName = null): void
    {
        $this->log('ACTION', $message, $serverName, $userName);
    }

    public function warning(string $message, ?string $serverName = null, ?string $userName = null): void
    {
        $this->log('WARNING', $message, $serverName, $userName);
    }

    public function error(string $message, ?string $serverName = null, ?string $userName = null): void
    {
        $this->log('ERROR', $message, $serverName, $userName);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPath(): string
    {
        return $this->logPath;
    }
}
