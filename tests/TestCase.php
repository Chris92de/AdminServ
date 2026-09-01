<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function assertStringContains(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertStringContainsString($needle, $haystack, $message);
    }
}
