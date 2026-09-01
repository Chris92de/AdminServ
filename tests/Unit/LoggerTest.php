<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Logger;

class LoggerTest extends TestCase
{
    private string $tempLogFile;

    protected function setUp(): void
    {
        $this->tempLogFile = sys_get_temp_dir() . '/adminserv_test_' . uniqid() . '.log';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempLogFile)) {
            @unlink($this->tempLogFile);
        }
    }

    public function testLoggerWritesFormattedMessages(): void
    {
        $config = [
            'logs' => [
                'enabled' => true,
                'path' => $this->tempLogFile,
            ],
        ];

        $logger = new Logger($config);
        $this->assertTrue($logger->isEnabled());
        $this->assertEquals($this->tempLogFile, $logger->getPath());

        $logger->info("Test info message", "Server1", "SuperAdmin");
        $logger->action("Kicked player 'testUser'", "Server1", "SuperAdmin");
        $logger->warning("Failed login attempt", "Server1");
        $logger->error("Connection timeout", "Server1");

        $this->assertFileExists($this->tempLogFile);
        $content = file_get_contents($this->tempLogFile);

        $this->assertStringContains('[INFO] [SuperAdmin@', $content);
        $this->assertStringContains('[Server1] Test info message', $content);

        $this->assertStringContains('[ACTION] [SuperAdmin@', $content);
        $this->assertStringContains("[Server1] Kicked player 'testUser'", $content);

        $this->assertStringContains('[WARNING] [Guest@', $content);
        $this->assertStringContains('[Server1] Failed login attempt', $content);

        $this->assertStringContains('[ERROR] [Guest@', $content);
        $this->assertStringContains('[Server1] Connection timeout', $content);
    }

    public function testDisabledLoggerDoesNotWrite(): void
    {
        $config = [
            'logs' => [
                'enabled' => false,
                'path' => $this->tempLogFile,
            ],
        ];

        $logger = new Logger($config);
        $this->assertFalse($logger->isEnabled());

        $logger->info("This should not be written");
        $this->assertFileDoesNotExist($this->tempLogFile);
    }
}
