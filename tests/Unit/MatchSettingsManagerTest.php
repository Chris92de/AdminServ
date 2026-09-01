<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MatchSettingsManager;

class MatchSettingsManagerTest extends TestCase
{
    private string $tempDir;
    private MatchSettingsManager $manager;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/ms_test_' . uniqid() . DIRECTORY_SEPARATOR;
        @mkdir($this->tempDir, 0777, true);
        $this->manager = new MatchSettingsManager($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . 'MatchSettings/*');
            if (is_array($files)) {
                foreach ($files as $f) {
                    @unlink($f);
                }
                @rmdir($this->tempDir . 'MatchSettings');
            }
            @rmdir($this->tempDir);
        }
    }

    public function testCreateAndReadXmlMatchSettings(): void
    {
        $filename = 'test_playlist.txt';
        $maps = ['Campaigns/Map1.Map.Gbx', 'Campaigns/Map2.Map.Gbx'];
        $gameMode = 1; // TimeAttack

        $savedRelPath = $this->manager->saveMatchSettings($filename, $maps, $gameMode, 'TM_TimeAttack_Online.Script.txt');
        $this->assertFileExists($this->tempDir . $savedRelPath);

        $parsed = $this->manager->parseFile($savedRelPath);
        $this->assertEquals(2, count($parsed['maps']));
        $this->assertEquals('Campaigns/Map1.Map.Gbx', $parsed['maps'][0]['file']);
        $this->assertEquals('Campaigns/Map2.Map.Gbx', $parsed['maps'][1]['file']);
        $this->assertEquals(1, $parsed['game_mode']);
        $this->assertEquals('TM_TimeAttack_Online.Script.txt', $parsed['script_name']);
    }

    public function testListFiles(): void
    {
        $this->manager->saveMatchSettings('playlist_a.txt', ['MapA.Map.Gbx'], 0);
        $this->manager->saveMatchSettings('playlist_b.xml', ['MapB.Map.Gbx'], 1);

        $list = $this->manager->listFiles('MatchSettings');
        $this->assertEquals(2, count($list));
    }

    public function testParseTm2020PlatformMatchSettingsFixture(): void
    {
        $fixturesDir = dirname(__DIR__) . '/fixtures/';
        $fixtureManager = new MatchSettingsManager($fixturesDir);

        $parsed = $fixtureManager->parseFile('MatchSettings_TM2020_Platform.txt');
        $this->assertEquals('MatchSettings_TM2020_Platform.txt', $parsed['file_name']);
        $this->assertEquals('Trackmania/TM_Platform_Online', $parsed['script_name']);
        $this->assertEquals(5, $parsed['map_count']);
        $this->assertEquals('Platform/Lemonpeaks.Map.Gbx', $parsed['maps'][0]['file']);
        $this->assertEquals('Platform/Tangerine.Map.Gbx', $parsed['maps'][4]['file']);

        // Verify parsed script settings
        $this->assertArrayHasKey('S_ChatTime', $parsed['mode_script_settings']);
        $this->assertEquals(15, $parsed['mode_script_settings']['S_ChatTime']['value']);
        $this->assertEquals('integer', $parsed['mode_script_settings']['S_ChatTime']['type']);
    }
}
