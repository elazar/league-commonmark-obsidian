<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedPdfRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->attachmentResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedPdfRenderer($this->attachmentResolver);
});

test('renders supported PDF extension without parameters', function () {
    $extension = 'pdf';
    $name = 'foo.' . $extension;
    $match = new EmbedParserMatch($name, $extension);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('iframe', $element->getTagName());
    $this->assertSame($name, $element->getAttribute('src'));
    $this->assertSame('100%', $element->getAttribute('width'));
});

test('renders supported PDF extension with PDF page number', function () {
    $extension = 'pdf';
    $name = 'foo.' . $extension;
    $page = 2;
    $match = new EmbedParserMatch($name, $extension, null, null, $page);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('iframe', $element->getTagName());
    $this->assertSame("$name#page=$page", $element->getAttribute('src'));
    $this->assertSame('100%', $element->getAttribute('width'));
});

test('does not render unsupported extensions', function () {
    $match = new EmbedParserMatch('unsupported.foo', 'foo');
    $this->assertNull($this->renderer->render($match));
});
