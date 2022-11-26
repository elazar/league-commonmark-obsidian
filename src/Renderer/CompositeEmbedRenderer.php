<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use League\CommonMark\Util\HtmlElement;

class CompositeEmbedRenderer implements EmbedRendererInterface
{
    /**
     * @var EmbedRendererInterface[]
     */
    private array $renderers;

    public function __construct(
        EmbedRendererInterface... $renderers
    ) {
        $this->renderers = $renderers;
    }

    public function setFromPath(string $fromPath): void
    {
        foreach ($this->renderers as $renderer) {
            $renderer->setFromPath($fromPath);
        }
    }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        foreach ($this->renderers as $renderer) {
            $element = $renderer->render($match);
            if ($element !== null) {
                return $element;
            }
        }
        return null;
    }
}
