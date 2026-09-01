<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\Gbx\GbxRemoteClient;
use RuntimeException;
use Throwable;

class ServerManager
{
    private array $config;
    private ?GbxRemoteClient $client = null;
    private ?string $activeServer = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getServers(): array
    {
        $servers = $this->config['servers'] ?? [];
        $list = [];
        foreach ($servers as $name => $srv) {
            $list[] = [
                'name' => $name,
                'address' => $srv['address'] ?? '127.0.0.1',
                'port' => $srv['port'] ?? 5000,
                'timeout' => $srv['timeout'] ?? 3,
                'maps_base_path' => $srv['maps_base_path'] ?? '',
                'matchsettings_path' => $srv['matchsettings_path'] ?? 'MatchSettings/',
                'admin_passwords' => $srv['admin_passwords'] ?? [
                    'SuperAdmin' => 'SuperAdmin',
                    'Admin' => 'Admin',
                    'User' => 'User',
                ],
            ];
        }
        return $list;
    }

    public function getServerConfig(string $serverName = ''): ?array
    {
        if ($serverName === '' || !isset($this->config['servers'][$serverName])) {
            $servers = $this->config['servers'] ?? [];
            if (!empty($servers)) {
                return reset($servers);
            }
            return null;
        }
        return $this->config['servers'][$serverName] ?? null;
    }

    public function getClient(string $serverName = '', ?string $adminLevel = null): GbxRemoteClient
    {
        if ($serverName === '' || !isset($this->config['servers'][$serverName])) {
            $keys = array_keys($this->config['servers'] ?? []);
            $serverName = $keys[0] ?? '';
        }

        if ($this->client !== null && $this->activeServer === $serverName && $this->client->isConnected()) {
            return $this->client;
        }

        $srv = $this->getServerConfig($serverName);
        if (!$srv) {
            throw new RuntimeException("Server '{$serverName}' not found in configuration.");
        }

        $host = $srv['address'] ?? '127.0.0.1';
        $port = (int)($srv['port'] ?? 5000);
        $timeout = (float)($srv['timeout'] ?? 3.0);

        $client = new GbxRemoteClient($host, $port, $timeout);
        $client->connect();

        // If admin level provided, authenticate
        if ($adminLevel !== null) {
            $pw = $srv['admin_passwords'][$adminLevel] ?? '';
            $client->authenticate($adminLevel, $pw);
        }

        $this->client = $client;
        $this->activeServer = $serverName;
        return $this->client;
    }

    public function testConnection(string $host, int $port, float $timeout = 3.0, ?string $login = null, ?string $password = null): array
    {
        try {
            $client = new GbxRemoteClient($host, $port, $timeout);
            $client->connect();

            $version = null;
            if ($login && $password) {
                $client->authenticate($login, $password);
                $version = $client->query('GetVersion');
            }

            return [
                'success' => true,
                'message' => 'Connection to dedicated server successful!',
                'version' => $version,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function saveServer(string $originalName, array $data, string $configFile): bool
    {
        $name = trim((string)($data['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('Server name cannot be empty.');
        }

        $serverEntry = [
            'address' => (string)($data['address'] ?? '127.0.0.1'),
            'port' => (int)($data['port'] ?? 5000),
            'timeout' => (float)($data['timeout'] ?? 3),
            'maps_base_path' => (string)($data['maps_base_path'] ?? ''),
            'matchsettings_path' => (string)($data['matchsettings_path'] ?? 'MatchSettings/'),
            'admin_passwords' => [
                'SuperAdmin' => (string)($data['admin_passwords']['SuperAdmin'] ?? 'SuperAdmin'),
                'Admin' => (string)($data['admin_passwords']['Admin'] ?? 'Admin'),
                'User' => (string)($data['admin_passwords']['User'] ?? 'User'),
            ],
        ];

        // Load existing config file
        if (!file_exists($configFile)) {
            throw new RuntimeException("Configuration file not found: {$configFile}");
        }

        $fullConfig = require $configFile;
        if (!isset($fullConfig['servers'])) {
            $fullConfig['servers'] = [];
        }

        // If editing name, remove old entry
        if ($originalName !== '' && $originalName !== $name && isset($fullConfig['servers'][$originalName])) {
            unset($fullConfig['servers'][$originalName]);
        }

        $fullConfig['servers'][$name] = $serverEntry;
        $this->config = $fullConfig;

        return $this->writeConfigFile($configFile, $fullConfig);
    }

    public function deleteServer(string $name, string $configFile): bool
    {
        if (!file_exists($configFile)) {
            throw new RuntimeException("Configuration file not found: {$configFile}");
        }

        $fullConfig = require $configFile;
        if (isset($fullConfig['servers'][$name])) {
            unset($fullConfig['servers'][$name]);
            $this->config = $fullConfig;
            return $this->writeConfigFile($configFile, $fullConfig);
        }

        return false;
    }

    public function updateMasterPassword(string $newPassword, string $configFile): bool
    {
        if (trim($newPassword) === '') {
            throw new RuntimeException('New password cannot be empty.');
        }

        if (!file_exists($configFile)) {
            throw new RuntimeException("Configuration file not found: {$configFile}");
        }

        $fullConfig = require $configFile;
        if (!isset($fullConfig['master_config'])) {
            $fullConfig['master_config'] = ['enabled' => true];
        }

        $fullConfig['master_config']['password'] = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->config = $fullConfig;

        return $this->writeConfigFile($configFile, $fullConfig);
    }

    private function writeConfigFile(string $configFile, array $config): bool
    {
        $export = var_export($config, true);

        // Preserve master configuration password recovery instructions
        $export = preg_replace_callback(
            "/('master_config'\s*=>\s*array\s*\(\s*'enabled'\s*=>\s*true,\s*)('password'\s*=>)/",
            function ($m) {
                $comment = "    // If you forgot your custom master password, you can reset it to the default \"adminserv\" password using:\n"
                    . "    // 'password' => '\$2y\$12\$R/eyMnxKZL4Igqk.dqiFQeKTCu5JhPutOCALcEY1eETgg9AMvidtC', // default: adminserv\n";
                return $m[1] . $comment . '    ' . $m[2];
            },
            $export
        );

        $content = "<?php\ndeclare(strict_types=1);\n\nreturn {$export};\n";

        if (@file_put_contents($configFile, $content, LOCK_EX) === false) {
            throw new RuntimeException("Unable to write to configuration file {$configFile}. Check file permissions.");
        }

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($configFile, true);
        }

        return true;
    }
}
