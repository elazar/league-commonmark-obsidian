<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedMarkdownRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->internalLinkResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedMarkdownRenderer($this->internalLinkResolver);
});

test('renders supported extension', function () {
    $match = new EmbedParserMatch('markdown.md', 'md');
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('iframe', $element->getTagName());
    $this->assertSame('markdown', $element->getAttribute('src'));
});

test('does not render unsupported extension', function () {
    $match = new EmbedParserMatch('unsupported.foo', 'foo');
    $this->assertNull($this->renderer->render($match));
});
