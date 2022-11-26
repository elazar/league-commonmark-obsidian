<?php

namespace Elazar\LeagueCommonMarkObsidian\Formatter;

class IdentityFormatter implements FormatterInterface
{
    public function format(string $html, string $markdownFilePath): string
    {
        return $html;
    }
}