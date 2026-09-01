<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MapBrowser;

class MapBrowserTest extends TestCase
{
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = dirname(__DIR__) . '/fixtures/';
    }

    public function testListDirectoryFindsTmnfChallengeAndTm2020Maps(): void
    {
        $browser = new MapBrowser($this->fixturesDir, ['gbx', 'map.gbx', 'challenge.gbx']);
        $result = $browser->listDirectory('');

        $this->assertArrayHasKey('files', $result);
        $fileNames = array_column($result['files'], 'name');

        // Verify TMNF .Challenge.gbx is listed
        $this->assertTrue(in_array('TMNF - A01-Race.Challenge.gbx', $fileNames, true));

        // Verify ManiaPlanet .Map.gbx is listed
        $this->assertTrue(in_array('MP - Lagoon A01.Map.gbx', $fileNames, true));

        // Verify TM2020 .Map.Gbx is listed
        $this->assertTrue(in_array('TM2020 - Training - 01.Map.Gbx', $fileNames, true));

        // Find TMNF map in results and check metadata
        $tmnfMap = null;
        foreach ($result['files'] as $f) {
            if ($f['name'] === 'TMNF - A01-Race.Challenge.gbx') {
                $tmnfMap = $f;
                break;
            }
        }

        $this->assertNotNull($tmnfMap);
        $this->assertEquals('A01-Race', $tmnfMap['map_name']);
        $this->assertEquals('Nadeo', $tmnfMap['author']);
        $this->assertEquals(24540, $tmnfMap['author_time']);
        $this->assertTrue($tmnfMap['has_thumbnail']);
    }
}
