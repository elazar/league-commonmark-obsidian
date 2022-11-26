<?php

namespace Elazar\LeagueCommonMarkObsidian\Parser;

use Elazar\LeagueCommonMarkObsidian\ReadonlyTrait;

class EmbedParserMatch
{
    use ReadonlyTrait;

    public function __construct(
        private string $embedName,
        private string $embedExtension,
        private ?int $imageWidth = null,
        private ?int $imageHeight = null,
        private ?int $pdfPage = null,
    ) { }
}
