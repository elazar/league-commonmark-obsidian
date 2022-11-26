<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use League\CommonMark\Util\HtmlElement;

interface EmbedRendererInterface
{
    /**
     * @return ?HtmlElement Element for the given embed if this renderer can
     *                      return one, or NULL if it cannot
     */
    public function render(EmbedParserMatch $match): ?HtmlElement;

    /**
     * Used to pass the path of the Markdown file being parsed to the renderer
     * so that links can be resolved relative to that file.
     */
    public function setFromPath(string $fromPath): void;
}
