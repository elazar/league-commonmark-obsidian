<?php

use Elazar\LeagueCommonMarkObsidian\Parser\InternalLinkParser;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\InlineParserEngine;
use League\CommonMark\Reference\ReferenceMap;

function getParseEngine(LinkResolverInterface $internalLinkResolver, ?string $fromPath = null): InlineParserEngine
{
    $internalLinkParser = new InternalLinkParser($internalLinkResolver);
    if ($fromPath) {
        $internalLinkParser->setFromPath($fromPath);
    }
    $environment = new Environment;
    $environment->addInlineParser($internalLinkParser);
    $engine = new InlineParserEngine($environment, new ReferenceMap);
    return $engine;
}

class FakeLinkResolver implements LinkResolverInterface {
    public function resolve(string $name, string $fromPath): string {
        return "{$fromPath}path/to/$name.html";
    }
}

test('parses internal link', function (string $markdown, string $linkUrl, string $linkText) {
    $engine = getParseEngine(new FakeLinkResolver, '');
    
    $block = new Paragraph;
    $engine->parse($markdown, $block);

    /** @var Link $link */
    $link = $block->firstChild();
    $this->assertInstanceOf(Link::class, $link);
    $this->assertSame($linkUrl, $link->getUrl());

    /** @var Text $label */
    $label = $link->firstChild();
    $this->assertInstanceOf(Text::class, $label);
    $this->assertSame($linkText, $label->getLiteral());
})->with([
    'without anchor or text' => [
        '[[LinkName]]',
        'path/to/LinkName.html',
        'LinkName',
    ],
    'with anchor and without text' => [
        '[[LinkName#LinkAnchor]]',
        'path/to/LinkName.html#LinkAnchor',
        'LinkName',
    ],
    'without anchor and with text' => [
        '[[LinkName|LinkText]]',
        'path/to/LinkName.html',
        'LinkText',
    ],
    'with anchor and text' => [
        '[[LinkName#LinkAnchor|LinkText]]',
        'path/to/LinkName.html#LinkAnchor',
        'LinkText',
    ],
]);

test('does not parse embeds', function () {
    $engine = getParseEngine(new FakeLinkResolver);

    $block = new Paragraph;
    $engine->parse('![[LinkName]]', $block);

    /** @var Text $child */
    $child = $block->firstChild();
    $this->assertInstanceOf(Text::class, $child);
    $this->assertSame('![[LinkName]]', $child->getLiteral());
});
