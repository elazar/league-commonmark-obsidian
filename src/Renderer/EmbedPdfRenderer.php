<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use League\CommonMark\Util\HtmlElement;

class EmbedPdfRenderer implements EmbedRendererInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $attachmentLinkResolver,
    ) { }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        if (!$this->isPdfExtension($match->embedExtension)) {
            return null;
        }
        $linkUrl = $this->attachmentLinkResolver->resolve($match->embedName, $this->fromPath);
        if ($match->pdfPage) {
            $linkUrl .= '#page=' . $match->pdfPage;
        }
        return new HtmlElement('iframe', ['src' => $linkUrl, 'width' => '100%']);
    }

    protected function isPdfExtension(string $extension): bool
    {
        return $extension === 'pdf';
    }
}
