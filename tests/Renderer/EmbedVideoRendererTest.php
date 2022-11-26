<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedVideoRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->attachmentResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedVideoRenderer($this->attachmentResolver);
});

test('renders supported video extensions', function (string $extension, string $type) {
    $name = 'video.' . $extension;
    $match = new EmbedParserMatch($name, $extension);
    
    $element = $this->renderer->render($match);
    
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('video', $element->getTagName());
    $this->assertTrue($element->getAttribute('controls'));
    
    $actual = $element->getContents();
    $expected = '<source src="' . $name . '" type="' . $type . '"></source>';
    $this->assertSame($expected, $actual);
})->with(function () {
    foreach (EmbedVideoRenderer::getVideoExtensions() as $extension => $type) {
        yield $extension => [$extension, $type];
    }
});

test('does not render unsupported extensions', function () {
    $match = new EmbedParserMatch('unsupported.foo', 'foo');
    $this->assertNull($this->renderer->render($match));
});