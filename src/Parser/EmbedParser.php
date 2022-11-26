<?php

namespace Elazar\LeagueCommonMarkObsidian\Parser;

use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererException;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

/**
 * Parses Obsidian embeds.
 *
 * @see https://help.obsidian.md/How+to/Format+your+notes#Embeds
 * @see https://help.obsidian.md/How+to/Embed+files
 */
class EmbedParser implements InlineParserInterface
{
    public function __construct(
        private EmbedRendererInterface $renderer,
    ) { }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('!\\[\\[([^\\|\\]#]+)(?:(?:\\|([0-9]+)(?:x([0-9]+))?)|(?:#page=([0-9]+))?)\\]\\]');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $subMatches = $inlineContext->getSubMatches();
        $embedName = $subMatches[0];
        $imageWidth = $imageHeight = $pdfPage = null;
        if (isset($subMatches[1]) && $subMatches[1] !== '') {
            $imageWidth = (int) $subMatches[1];
            if (isset($subMatches[2]) && $subMatches[2] !== '') {
                $imageHeight = (int) $subMatches[2];
            }
        } elseif (isset($subMatches[3]) && $subMatches[3] !== '') {
            $pdfPage = (int) $subMatches[3];
        }
        $embedExtension = ltrim(strrchr($embedName, '.'), '.');
        $match = new EmbedParserMatch($embedName, $embedExtension, $imageWidth, $imageHeight, $pdfPage);
        $element = $this->renderer->render($match);
        if ($element === null) {
            throw EmbedRendererException::failedToRender($match->embedName);
        }
        $inlineContext->getContainer()->appendChild(new HtmlInline($element));
        return true;
    }
}

