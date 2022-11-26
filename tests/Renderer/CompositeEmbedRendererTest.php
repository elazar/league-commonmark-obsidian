<?php

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\CompositeEmbedRenderer;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererInterface;
use League\CommonMark\Util\HtmlElement;

beforeEach(function () {
    $this->match = new EmbedParserMatch('EmbedName.md', 'md');
    $this->element = new HtmlElement('a');
    $this->embedRenderer = new class($this->element) implements EmbedRendererInterface {
        use FromPathTrait;
        public function __construct(private HtmlElement $element) { }
        public function render(EmbedParserMatch $match): ?HtmlElement {
            return $this->element;
        }
    };
    $this->nullRenderer = new class implements EmbedRendererInterface {
        use FromPathTrait;
        public function render(EmbedParserMatch $match): ?HtmlElement {
            return null;
        }
    };
});

test('renders with a single renderer that resolves', function () {
    $renderer = new CompositeEmbedRenderer($this->embedRenderer);
    $this->assertSame($this->element, $renderer->render($this->match));
});

test('renders with multiple renderers where one resolves', function () {
    $renderer = new CompositeEmbedRenderer($this->nullRenderer, $this->embedRenderer);
    $this->assertSame($this->element, $renderer->render($this->match));
});

test('does not render with a single renderer that does not resolve', function () {
    $renderer = new CompositeEmbedRenderer($this->nullRenderer);
    $this->assertNull($renderer->render($this->match));
});

test('does not render with multiple renderers that do not resolve', function () {
    $renderer = new CompositeEmbedRenderer($this->nullRenderer, $this->nullRenderer);
    $this->assertNull($renderer->render($this->match));
});
