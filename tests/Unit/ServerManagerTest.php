<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ServerManager;

class ServerManagerTest extends TestCase
{
    private string $tempConfigFile;
    private array $initialConfig;
    private ServerManager $manager;

    protected function setUp(): void
    {
        $this->tempConfigFile = sys_get_temp_dir() . '/adminserv_test_' . uniqid() . '.php';
        $this->initialConfig = [
            'master_config' => [
                'enabled' => true,
                'password' => password_hash('adminserv', PASSWORD_BCRYPT),
            ],
            'servers' => [
                'Server1' => [
                    'address' => '127.0.0.1',
                    'port' => 5000,
                    'timeout' => 3,
                    'maps_base_path' => '/tmp/maps',
                    'matchsettings_path' => 'MatchSettings/',
                    'admin_passwords' => [
                        'SuperAdmin' => 'SuperAdmin',
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ],
                ],
            ],
        ];

        file_put_contents(
            $this->tempConfigFile,
            "<?php\nreturn " . var_export($this->initialConfig, true) . ";\n"
        );

        $this->manager = new ServerManager($this->initialConfig);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigFile)) {
            @unlink($this->tempConfigFile);
        }
    }

    public function testGetServersReturnsList(): void
    {
        $servers = $this->manager->getServers();
        $this->assertEquals(1, count($servers));
        $this->assertEquals('Server1', $servers[0]['name']);
        $this->assertEquals(5000, $servers[0]['port']);
    }

    public function testSaveServerAddsNewProfile(): void
    {
        $newServer = [
            'name' => 'Server2',
            'address' => '127.0.0.1',
            'port' => 5001,
            'timeout' => 2.5,
            'maps_base_path' => '',
            'matchsettings_path' => '',
            'admin_passwords' => [
                'SuperAdmin' => 'secret',
                'Admin' => 'secret',
                'User' => 'secret',
            ],
        ];

        $saved = $this->manager->saveServer('', $newServer, $this->tempConfigFile);
        $this->assertTrue($saved);

        $writtenConfig = require $this->tempConfigFile;
        $this->assertArrayHasKey('Server2', $writtenConfig['servers']);
        $this->assertEquals(5001, $writtenConfig['servers']['Server2']['port']);
    }

    public function testDeleteServerRemovesProfile(): void
    {
        $deleted = $this->manager->deleteServer('Server1', $this->tempConfigFile);
        $this->assertTrue($deleted);

        $writtenConfig = require $this->tempConfigFile;
        $this->assertFalse(isset($writtenConfig['servers']['Server1']));
    }

    public function testUpdateMasterPassword(): void
    {
        $updated = $this->manager->updateMasterPassword('newMaster999', $this->tempConfigFile);
        $this->assertTrue($updated);

        $writtenConfig = require $this->tempConfigFile;
        $this->assertTrue(password_verify('newMaster999', $writtenConfig['master_config']['password']));
    }
}
