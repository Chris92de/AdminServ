<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AuthManager;

class AuthManagerTest extends TestCase
{
    private array $config;
    private AuthManager $auth;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];

        $this->config = [
            'master_config' => [
                'enabled' => true,
                'password' => password_hash('master123', PASSWORD_BCRYPT),
            ],
            'servers' => [
                'ServerA' => [
                    'admin_passwords' => [
                        'SuperAdmin' => 'super_pass',
                        'Admin' => 'admin_pass',
                        'User' => 'user_pass',
                    ],
                ],
            ],
            'admin_levels' => [
                'SuperAdmin' => [
                    'name' => 'SuperAdmin',
                    'permissions' => ['maps_manage', 'player_kick', 'server_options'],
                ],
                'Admin' => [
                    'name' => 'Admin',
                    'permissions' => ['player_kick'],
                ],
                'User' => [
                    'name' => 'User',
                    'permissions' => [],
                ],
            ],
        ];

        $this->auth = new AuthManager($this->config);
    }

    public function testInitialStateUnauthenticated(): void
    {
        $this->assertFalse($this->auth->isLoggedIn());
        $this->assertNull($this->auth->getUser());
        $this->assertFalse($this->auth->isConfigUnlocked());
    }

    public function testLoginSuccessWithLevel(): void
    {
        $res = $this->auth->login('ServerA', 'super_pass', 'SuperAdmin');
        $this->assertTrue($res);
        $this->assertTrue($this->auth->isLoggedIn());

        $user = $this->auth->getUser();
        $this->assertNotNull($user);
        $this->assertEquals('SuperAdmin', $user['level']);
        $this->assertEquals('ServerA', $this->auth->getActiveServer());
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $res = $this->auth->login('ServerA', 'wrong_pass', 'SuperAdmin');
        $this->assertFalse($res);
        $this->assertFalse($this->auth->isLoggedIn());
    }

    public function testLoginFailsWithWrongRolePassword(): void
    {
        // Trying to login as SuperAdmin using User password
        $res = $this->auth->login('ServerA', 'user_pass', 'SuperAdmin');
        $this->assertFalse($res);
        $this->assertFalse($this->auth->isLoggedIn());
    }

    public function testPermissionsCheck(): void
    {
        $this->auth->login('ServerA', 'admin_pass', 'Admin');
        $this->assertTrue($this->auth->hasPermission('player_kick'));
        $this->assertFalse($this->auth->hasPermission('server_options'));
    }

    public function testMasterConfigUnlockAndLock(): void
    {
        $this->assertFalse($this->auth->isConfigUnlocked());

        $wrong = $this->auth->unlockConfig('wrong');
        $this->assertFalse($wrong);
        $this->assertFalse($this->auth->isConfigUnlocked());

        $correct = $this->auth->unlockConfig('master123');
        $this->assertTrue($correct);
        $this->assertTrue($this->auth->isConfigUnlocked());

        $this->auth->lockConfig();
        $this->assertFalse($this->auth->isConfigUnlocked());
    }

    public function testLogoutClearsSession(): void
    {
        $this->auth->login('ServerA', 'super_pass', 'SuperAdmin');
        $this->assertTrue($this->auth->isLoggedIn());

        $this->auth->logout();
        $this->assertFalse($this->auth->isLoggedIn());
        $this->assertNull($this->auth->getUser());
    }
}
