<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Gbx\GbxFetcher;

class GbxFetcherTest extends TestCase
{
    private string $fixturesDir;
    private string $tempFile;

    protected function setUp(): void
    {
        $this->fixturesDir = dirname(__DIR__) . '/fixtures/';
        $this->tempFile = sys_get_temp_dir() . '/test_map_' . uniqid() . '.Map.Gbx';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            @unlink($this->tempFile);
        }
    }

    public function testNonExistentFileReturnsDefaults(): void
    {
        $gbx = new GbxFetcher('/non/existent/path/sample.Map.Gbx');
        $data = $gbx->getData();

        $this->assertEquals('sample.Map.Gbx', $data['name']);
        $this->assertEquals('Unknown', $data['author']);
        $this->assertEquals(0, $data['authorTime']);
        $this->assertFalse($data['hasThumbnail']);
        $this->assertNull($gbx->getThumbnail());
    }

    public function testInvalidHeaderReturnsEmptyDefaults(): void
    {
        file_put_contents($this->tempFile, 'INVALID_BINARY_HEADER');
        $gbx = new GbxFetcher($this->tempFile);
        $data = $gbx->getData();

        $this->assertEquals('Unknown', $data['author']);
        $this->assertEquals(0, $data['authorTime']);
        $this->assertFalse($data['hasThumbnail']);
    }

    public function testParseTmnfChallengeFixture(): void
    {
        $file = $this->fixturesDir . 'TMNF - A01-Race.Challenge.gbx';
        $this->assertFileExists($file);

        $gbx = new GbxFetcher($file);
        $data = $gbx->getData();

        $this->assertEquals('A01-Race', $data['name']);
        $this->assertEquals('Nadeo', $data['author']);
        $this->assertEquals('Stadium', $data['environment']);
        $this->assertEquals('BeySZdnfuSh4nHY5xztiXLmlrXe', $data['uid']);
        $this->assertEquals(24540, $data['authorTime']);
        $this->assertEquals(25870, $data['goldTime']);
        $this->assertEquals(29190, $data['silverTime']);
        $this->assertEquals(38030, $data['bronzeTime']);
        $this->assertTrue($data['hasThumbnail']);
        $this->assertGreaterThan(5000, strlen($gbx->getThumbnail() ?? ''));
    }

    public function testParseManiaplanetMapFixture(): void
    {
        $file = $this->fixturesDir . 'MP - Lagoon A01.Map.gbx';
        $this->assertFileExists($file);

        $gbx = new GbxFetcher($file);
        $data = $gbx->getData();

        $this->assertEquals('$fff$sA01', $data['name']);
        $this->assertEquals('Nadeo', $data['author']);
        $this->assertEquals('0Qme2CNzM_7yhhZlhu54VsxndIh', $data['uid']);
        $this->assertEquals(23851, $data['authorTime']);
        $this->assertEquals(25725, $data['goldTime']);
        $this->assertTrue($data['hasThumbnail']);
        $this->assertGreaterThan(10000, strlen($gbx->getThumbnail() ?? ''));
    }

    public function testParseTm2020MapFixture(): void
    {
        $file = $this->fixturesDir . 'TM2020 - Training - 01.Map.Gbx';
        $this->assertFileExists($file);

        $gbx = new GbxFetcher($file);
        $data = $gbx->getData();

        $this->assertEquals('Training - 01', $data['name']);
        $this->assertEquals('Nadeo', $data['author']);
        $this->assertEquals('Stadium', $data['environment']);
        $this->assertEquals('olsKnq_qAghcVAnEkoeUnVHFZei', $data['uid']);
        $this->assertEquals(7528, $data['authorTime']);
        $this->assertEquals(8000, $data['goldTime']);
        $this->assertTrue($data['hasThumbnail']);
        $this->assertGreaterThan(20000, strlen($gbx->getThumbnail() ?? ''));
    }
}
