<?php
declare(strict_types=1);

namespace App;

use App\Controllers\AdminConfigController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MapController;
use App\Controllers\MatchSettingsController;
use App\Controllers\PlayerController;
use App\Controllers\ServerConfigController;
use App\Services\AuthManager;
use App\Services\Logger;
use App\Services\ServerManager;
use Throwable;

class App
{
    public const VERSION = '4.0.0';

    private array $config;
    private ?string $configFile;
    private AuthManager $auth;
    private ServerManager $serverManager;
    private Logger $logger;

    public function __construct(array $config, ?string $configFile = null)
    {
        $this->config = $config;
        $this->configFile = $configFile ?? (dirname(__DIR__) . '/config/adminserv.php');
        $this->auth = new AuthManager($config);
        $this->serverManager = new ServerManager($config);
        $this->logger = new Logger($config);
    }

    public static function registerAutoloader(): void
    {
        spl_autoload_register(function (string $class) {
            $prefix = 'App\\';
            $baseDir = __DIR__ . '/';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function handleRequest(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        // Check if request is for compiled static assets in public/assets/
        if (str_starts_with($uri, '/assets/')) {
            $assetFile = __DIR__ . '/../public' . $uri;
            if (file_exists($assetFile) && is_file($assetFile)) {
                $ext = pathinfo($assetFile, PATHINFO_EXTENSION);
                $mime = match ($ext) {
                    'js' => 'application/javascript; charset=utf-8',
                    'css' => 'text/css; charset=utf-8',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'svg' => 'image/svg+xml',
                    'woff2' => 'font/woff2',
                    default => 'application/octet-stream',
                };
                header("Content-Type: {$mime}");
                header('Cache-Control: public, max-age=31536000');
                readfile($assetFile);
                exit;
            }
        }

        // Check if request is an API request
        if (str_starts_with($uri, '/api/')) {
            $this->processApiHttp($uri, $method);
            return;
        }

        // Otherwise, serve SPA index
        $this->serveSpa();
    }

    private function processApiHttp(string $uri, string $method): void
    {
        $path = substr($uri, 5); // strip '/api/'
        $cleanPath = parse_url($path, PHP_URL_PATH) ?? $path;

        // Binary/image streaming routes manage their own headers
        if ($cleanPath !== 'maps/thumbnail') {
            header('Content-Type: application/json; charset=utf-8');
        }

        // Parse JSON body for POST requests
        $body = [];
        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
            $raw = file_get_contents('php://input');
            if ($raw !== false && $raw !== '') {
                $body = json_decode($raw, true) ?: [];
            }
            if (empty($body) && !empty($_POST)) {
                $body = $_POST;
            }
        }

        $query = $_GET;

        try {
            $response = $this->dispatchRoute($path, $method, $query, $body);
            if ($cleanPath !== 'maps/thumbnail') {
                echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } catch (Throwable $e) {
            $this->logger->error("Unhandled API exception on [{$method} /{$path}]: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    public function handleApiRequest(string $method, string $path, array $body = [], array $query = []): mixed
    {
        return $this->dispatchRoute(trim($path, '/'), strtoupper($method), $query, $body);
    }

    public function dispatchRoute(string $path, string $method, array $query, array $body): mixed
    {
        $authController = new AuthController($this->auth, $this->serverManager, $this->config, $this->logger);
        $dashboardController = new DashboardController($this->auth, $this->serverManager, $this->logger);
        $playerController = new PlayerController($this->auth, $this->serverManager, $this->logger);
        $mapController = new MapController($this->auth, $this->serverManager, $this->config, $this->logger);
        $matchSettingsController = new MatchSettingsController($this->auth, $this->serverManager, $this->logger);
        $serverConfigController = new ServerConfigController($this->auth, $this->serverManager, $this->logger);
        $adminConfigController = new AdminConfigController($this->auth, $this->serverManager, $this->config, $this->configFile, $this->logger);

        $publicRoutes = [
            'GET /auth/me',
            'POST /auth/login',
            'POST /auth/switch-server',
            'GET /config/status',
            'POST /config/unlock',
            'GET /dashboard/status',
            'GET /dashboard/overview',
            'GET /maps/thumbnail',
        ];

        $masterConfigRoutes = [
            'POST /config/lock',
            'GET /config/servers',
            'POST /config/servers/save',
            'POST /config/servers/delete',
            'POST /config/servers/test',
            'POST /config/change-password',
            'GET /config/admin-levels',
        ];

        $cleanPath = trim((string)(parse_url($path, PHP_URL_PATH) ?: $path), '/');
        $currentRoute = "{$method} /{$cleanPath}";

        // 1. Guard Master Configuration endpoints with Master Password session check
        if (in_array($currentRoute, $masterConfigRoutes, true) && !$this->auth->isConfigUnlocked()) {
            http_response_code(403);
            return [
                'success' => false,
                'error' => 'Forbidden. Master configuration password required to access this endpoint.',
            ];
        }

        // 2. Guard Game Server Administration endpoints with Game Server Admin login check
        if (!in_array($currentRoute, $publicRoutes, true) && !in_array($currentRoute, $masterConfigRoutes, true) && !$this->auth->isLoggedIn()) {
            http_response_code(401);
            return [
                'success' => false,
                'error' => 'Authentication required. You must be logged in to access server administration features.',
            ];
        }

        return match ($currentRoute) {
            'GET /auth/me' => $authController->me(),
            'POST /auth/login' => $authController->login($body),
            'POST /auth/logout' => $authController->logout(),
            'POST /auth/switch-server' => $authController->switchServer($body),

            'GET /dashboard/overview' => $dashboardController->overview(),
            'GET /dashboard/status' => $dashboardController->status(),
            'GET /dashboard/chat' => $dashboardController->chat($query),
            'POST /dashboard/chat' => $dashboardController->sendChat($body),
            'POST /dashboard/action' => $dashboardController->action($body),

            'GET /players' => $playerController->index(),
            'POST /players/kick' => $playerController->kick($body),
            'POST /players/ban' => $playerController->ban($body),
            'POST /players/unban' => $playerController->unban($body),
            'POST /players/force-spectator' => $playerController->forceSpectator($body),
            'POST /players/force-player' => $playerController->forcePlayer($body),
            'GET /players/bans' => $playerController->bans(),

            'GET /maps/playlist' => $mapController->playlist(),
            'POST /maps/remove' => $mapController->remove($body),
            'POST /maps/insert' => $mapController->insert($body),
            'POST /maps/jump' => $mapController->jump($body),
            'GET /maps/local' => $mapController->local($query),
            'POST /maps/upload' => $mapController->upload($_FILES, $body),
            'GET /maps/thumbnail' => $mapController->thumbnail($query),

            'GET /matchsettings' => $matchSettingsController->index(),
            'GET /matchsettings/detail' => $matchSettingsController->detail($query),
            'POST /matchsettings/load' => $matchSettingsController->load($body),
            'POST /matchsettings/create' => $matchSettingsController->create($body),

            'GET /server/options' => $serverConfigController->getOptions(),
            'POST /server/options' => $serverConfigController->setOptions($body),
            'GET /server/game-infos' => $serverConfigController->getGameInfos(),
            'POST /server/game-infos' => $serverConfigController->setGameInfos($body),
            'GET /server/script-settings' => $serverConfigController->getScriptSettings(),
            'POST /server/script-settings' => $serverConfigController->setScriptSettings($body),
            'POST /server/script-mode' => $serverConfigController->setScriptMode($body),
            'GET /server/blacklist' => $serverConfigController->getBlacklist(),
            'POST /server/blacklist/save' => $serverConfigController->saveBlacklist($body),

            'GET /config/servers' => $adminConfigController->getServers(),
            'GET /config/status' => $adminConfigController->status(),
            'POST /config/unlock' => $adminConfigController->unlock($body),
            'POST /config/lock' => $adminConfigController->lock(),
            'POST /config/servers/save' => $adminConfigController->saveServer($body),
            'POST /config/servers/delete' => $adminConfigController->deleteServer($body),
            'POST /config/servers/test' => $adminConfigController->testServer($body),
            'POST /config/change-password' => $adminConfigController->changePassword($body),
            'GET /config/admin-levels' => $adminConfigController->getAdminLevels(),

            default => [
                'success' => false,
                'error' => "Endpoint not found: {$method} /api/{$path}",
            ],
        };
    }

    private function serveSpa(): void
    {
        $appTitle = htmlspecialchars($this->config['app']['title'] ?? 'AdminServ', ENT_QUOTES, 'UTF-8');
        $defaultTheme = htmlspecialchars($this->config['app']['default_theme'] ?? 'dark', ENT_QUOTES, 'UTF-8');

        // Check if Vite production build assets exist in public/assets
        $manifestPath = __DIR__ . '/../public/.vite/manifest.json';
        $jsFile = 'assets/index.js';
        $cssFile = file_exists(__DIR__ . '/../public/assets/main.css') ? 'assets/main.css' : 'assets/index.css';

        if (file_exists($manifestPath)) {
            $manifest = json_decode((string)file_get_contents($manifestPath), true);
            if (isset($manifest['src/main.tsx'])) {
                $jsFile = ($manifest['src/main.tsx']['file'] ?? 'assets/index.js');
                if (!empty($manifest['src/main.tsx']['css'][0])) {
                    $cssFile = $manifest['src/main.tsx']['css'][0];
                }
            }
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $defaultTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appTitle ?> - TrackMania & ManiaPlanet Server Manager</title>
    <link rel="stylesheet" href="/<?= $cssFile ?>">
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased">
    <div id="app"></div>
    <script type="module" src="/<?= $jsFile ?>"></script>
</body>
</html>
        <?php
        exit;
    }
}
