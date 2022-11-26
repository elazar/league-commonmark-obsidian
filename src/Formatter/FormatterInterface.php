<?php

namespace Elazar\LeagueCommonMarkObsidian\Formatter;

/**
 * Formats a given HTML string rendered from Markdown prior to writing it to
 * a file, e.g. by prepending a header, appending a footer, or performing
 * some other markup transformation.
 */
interface FormatterInterface
{
    public function format(string $html, string $markdownFilePath): string;
}