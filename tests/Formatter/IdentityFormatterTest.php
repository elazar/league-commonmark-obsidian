<?php

use Elazar\LeagueCommonMarkObsidian\Formatter\IdentityFormatter;

test('returns HTML unchanged', function () {
    $formatter = new IdentityFormatter;
    $html = '<html></html>';
    $markdownFilePath = '/path/to/markdown.md';
    $formatted = $formatter->format($html, $markdownFilePath);
    $this->assertSame($html, $formatted);
});