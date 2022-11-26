<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use League\CommonMark\Util\HtmlElement;

class EmbedMarkdownRenderer implements EmbedRendererInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $internalLinkResolver,
    ) { }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        if (!$this->isMarkdownExtension($match->embedExtension)) {
            return null;
        }
        $linkName = substr($match->embedName, 0, -1 * (strlen($match->embedExtension) + 1));
        $linkUrl = $this->internalLinkResolver->resolve($linkName, $this->fromPath);
        return new HtmlElement('iframe', ['src' => $linkUrl]);
    }

    protected function isMarkdownExtension(string $extension): bool
    {
        return $extension === 'md';
    }
}
