<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedImageRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->attachmentResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedImageRenderer($this->attachmentResolver);
});

test('renders supported image extensions', function (string $extension) {
    $name = 'image.' . $extension;
    $match = new EmbedParserMatch($name, $extension);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('img', $element->getTagName());
    $this->assertSame($name, $element->getAttribute('src'));
})->with(EmbedImageRenderer::getImageExtensions());

test('renders width', function () {
    $width = 100;
    $match = new EmbedParserMatch('image.gif', 'gif', $width);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('img', $element->getTagName());
    $this->assertEquals($width, $element->getAttribute('width'));
});

test('renders width and height', function () {
    $height = 100;
    $match = new EmbedParserMatch('image.gif', 'gif', 200, $height);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('img', $element->getTagName());
    $this->assertEquals($height, $element->getAttribute('height'));
});

test('does not render unsupported extensions', function () {
    $match = new EmbedParserMatch('unsupported.foo', 'foo');
    $this->assertNull($this->renderer->render($match));
});
