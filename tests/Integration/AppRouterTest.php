<?php
declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;
use App\App;

class AppRouterTest extends TestCase
{
    private array $config;
    private App $app;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];

        $this->config = [
            'app' => [
                'title' => 'AdminServ Test',
            ],
            'master_config' => [
                'enabled' => true,
                'password' => password_hash('master123', PASSWORD_BCRYPT),
            ],
            'servers' => [
                'TestServer' => [
                    'address' => '127.0.0.1',
                    'port' => 5000,
                    'timeout' => 1.0,
                    'maps_base_path' => '',
                    'matchsettings_path' => 'MatchSettings/',
                    'admin_passwords' => [
                        'SuperAdmin' => 'SuperAdmin',
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ],
                ],
            ],
            'admin_levels' => [
                'SuperAdmin' => ['name' => 'SuperAdmin', 'permissions' => ['maps_manage', 'player_kick']],
                'Admin' => ['name' => 'Admin', 'permissions' => ['player_kick']],
                'User' => ['name' => 'User', 'permissions' => []],
            ],
        ];

        $this->app = new App($this->config);
    }

    public function testPublicRouteAuthMe(): void
    {
        $res = $this->app->handleApiRequest('GET', 'auth/me');
        $this->assertFalse($res['authenticated']);
        $this->assertEquals('AdminServ Test', $res['app_info']['title']);
    }

    public function testPublicRouteDashboardOverview(): void
    {
        $res = $this->app->handleApiRequest('GET', 'dashboard/overview');
        $this->assertTrue($res['success']);
        $this->assertArrayHasKey('overview', $res);
    }

    public function testMasterConfigRouteForbiddenWhenLocked(): void
    {
        $res = $this->app->handleApiRequest('POST', 'config/change-password', [
            'new_password' => 'new123',
            'confirm_password' => 'new123',
        ]);
        $this->assertFalse($res['success']);
        $this->assertStringContains('Forbidden', $res['error']);
    }

    public function testServerAdminRouteUnauthorizedWhenNotLoggedIn(): void
    {
        $res = $this->app->handleApiRequest('GET', 'players');
        $this->assertFalse($res['success']);
        $this->assertStringContains('Authentication required', $res['error']);
    }

    public function testLoginFailsWithInvalidPassword(): void
    {
        $res = $this->app->handleApiRequest('POST', 'auth/login', [
            'server' => 'TestServer',
            'password' => 'wrong_password',
            'level' => 'SuperAdmin',
        ]);
        $this->assertFalse($res['success']);
    }

    public function testLoginSuccessSetsSession(): void
    {
        $res = $this->app->handleApiRequest('POST', 'auth/login', [
            'server' => 'TestServer',
            'password' => 'SuperAdmin',
            'level' => 'SuperAdmin',
        ]);
        $this->assertTrue($res['success']);
        $this->assertEquals('SuperAdmin', $res['user']['level']);
    }

    public function testSwitchServerRequiresCredentialsWhenLoggedIn(): void
    {
        // 1. Log into TestServer
        $this->app->handleApiRequest('POST', 'auth/login', [
            'server' => 'TestServer',
            'password' => 'SuperAdmin',
            'level' => 'SuperAdmin',
        ]);

        // Add a second server to test switching
        $configWithTwoServers = $this->config;
        $configWithTwoServers['servers']['SecondServer'] = [
            'address' => '127.0.0.1',
            'port' => 5001,
            'admin_passwords' => ['SuperAdmin' => 'Secret2'],
        ];
        $app = new App($configWithTwoServers);

        // 2. Switch server without password -> should log out and require auth
        $res = $app->handleApiRequest('POST', 'auth/switch-server', [
            'server' => 'SecondServer',
        ]);
        $this->assertTrue($res['success']);
        $this->assertFalse($res['authenticated']);
        $this->assertTrue($res['require_auth']);

        // 3. Authenticated routes should now be blocked
        $playerRes = $app->handleApiRequest('GET', 'players');
        $this->assertFalse($playerRes['success']);
    }
}
