<?php

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParser;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererException;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\InlineParserEngine;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Util\HtmlElement;

function parseWithRenderer(string $markdown, EmbedRendererInterface $renderer): void
{
    $environment = new Environment;
    $parser = new EmbedParser($renderer);
    $environment->addInlineParser($parser);
    $engine = new InlineParserEngine($environment, new ReferenceMap);
    $block = new Paragraph;
    $engine->parse($markdown, $block);
}

test('renders supported embed', function (string $markdown, EmbedParserMatch $expected) {
    $renderer = new class implements EmbedRendererInterface {
        use FromPathTrait;
        public EmbedParserMatch $match;
        public function render(EmbedParserMatch $match): ?HtmlElement {
            $this->match = $match;
            return new HtmlElement('a');
        }
    };
    parseWithRenderer($markdown, $renderer);
    $actual = $renderer->match;
    $this->assertSame($expected->embedName, $actual->embedName);
    $this->assertSame($expected->embedExtension, $actual->embedExtension);
    $this->assertSame($expected->imageWidth, $actual->imageWidth);
    $this->assertSame($expected->imageHeight, $actual->imageHeight);
    $this->assertSame($expected->pdfPage, $actual->pdfPage);
})->with([
    'with no parameters' => [
        '![[LinkName.pdf]]',
        new EmbedParserMatch('LinkName.pdf', 'pdf'),
    ],
    'with image width' => [
        '![[LinkName.gif|100]]',
        new EmbedParserMatch('LinkName.gif', 'gif', 100),
    ],
    'with image width and height' => [
        '![[LinkName.gif|100x200]]',
        new EmbedParserMatch('LinkName.gif', 'gif', 100, 200),
    ],
    'with PDF page number' => [
        '![[LinkName.pdf#page=2]]',
        new EmbedParserMatch('LinkName.pdf', 'pdf', null, null, 2),
    ],
]);

test('does not render unsupported embed', function () {
    $renderer = new class implements EmbedRendererInterface {
        use FromPathTrait;
        public function render(EmbedParserMatch $match): ?HtmlElement {
            return null;
        }
    };
    parseWithRenderer('![[LinkName.pdf]]', $renderer);
})->throws(EmbedRendererException::class);