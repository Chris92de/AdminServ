<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TmNickParser;

class TmNickParserTest extends TestCase
{
    public function testStripFormatting(): void
    {
        $input = '$f00Red $0f0Green $00fBlue $wBold $iItalic $sShadowed $zReset';
        $cleaned = TmNickParser::strip($input);
        $this->assertEquals('Red Green Blue Bold Italic Shadowed Reset', $cleaned);
    }

    public function testStripSpecialCharsAndDoubleDollar(): void
    {
        $input = 'Hello $$world and $g$h[link]Click here$h $zdone';
        $cleaned = TmNickParser::strip($input);
        $this->assertEquals('Hello $world and Click here done', $cleaned);
    }

    public function testToHtmlConvertsColors(): void
    {
        $input = '$f00Red Text$z Normal';
        $html = TmNickParser::toHtml($input);
        $this->assertStringContains('color: rgb(255,0,0)', $html);
        $this->assertStringContains('Red Text', $html);
        $this->assertStringContains('Normal', $html);
    }

    public function testToHtmlEscapesXss(): void
    {
        $input = '$f00<script>alert("xss")</script>';
        $html = TmNickParser::toHtml($input);
        $this->assertFalse(str_contains($html, '<script>'));
        $this->assertStringContains('&lt;script&gt;', $html);
    }

    public function testToHtmlFormattingStyles(): void
    {
        $input = '$w$i$sStyled Title$z';
        $html = TmNickParser::toHtml($input);
        $this->assertStringContains('font-weight: 700', $html);
        $this->assertStringContains('font-style: italic', $html);
        $this->assertStringContains('text-shadow:', $html);
    }

    public function testEmptyString(): void
    {
        $this->assertEquals('', TmNickParser::strip(''));
        $this->assertEquals('', TmNickParser::toHtml(''));
    }
}
