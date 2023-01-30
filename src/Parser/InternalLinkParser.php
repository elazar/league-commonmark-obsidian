<?php

namespace Elazar\LeagueCommonMarkObsidian\Parser;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

/**
 * Parses Obsidian internal links.
 *
 * @see https://help.obsidian.md/How+to/Format+your+notes#Internal+linking
 * @see https://help.obsidian.md/How+to/Internal+link#Link+formats
 */
class InternalLinkParser implements InlineParserInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $internalLinkResolver,
    ) { }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('(?<!!)\\[\\[([^\\]\\|#]+)?(?:#([^\\|]+))?(?:\\|([^\\]]+))?\\]\\]');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $subMatches = $inlineContext->getSubMatches();
        $linkName = $subMatches[0];
        $linkAnchor = $subMatches[1] ?? null;
        $linkText = $subMatches[2] ?? $linkName ?: $linkAnchor;
        $linkUrl = $linkName ? $this->internalLinkResolver->resolve($linkName, $this->fromPath) : '';
        if ($linkAnchor) {
            $linkUrl .= '#' . $linkAnchor;
        }
        $inlineContext->getContainer()->appendChild(new Link($linkUrl, $linkText));
        return true;
    }
}
