<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedAudioRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->attachmentResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedAudioRenderer($this->attachmentResolver);
});

test('renders supported audio extensions', function (string $extension) {
    $name = 'audio.' . $extension;
    $match = new EmbedParserMatch($name, $extension);
    $element = $this->renderer->render($match);
    $this->assertInstanceOf(HtmlElement::class, $element);
    $this->assertSame('audio', $element->getTagName());
    $this->assertTrue($element->getAttribute('controls'));
    $this->assertSame($name, $element->getAttribute('src'));
})->with(EmbedAudioRenderer::getAudioExtensions());

test('does not render unsupported extensions', function () {
    $match = new EmbedParserMatch('unsupported.foo', 'foo');
    $this->assertNull($this->renderer->render($match));
});
