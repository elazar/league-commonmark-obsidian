<?php

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRenderer;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;

beforeEach(function () {
    $this->attachmentResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->internalLinkResolver = new class implements LinkResolverInterface {
        public function resolve(string $name, string $fromPath): string {
            return $name;
        }
    };
    $this->renderer = new EmbedRenderer(
        $this->internalLinkResolver,
        $this->attachmentResolver,
    );
});

test('renders audio', function () {
    $match = new EmbedParserMatch('audio.mp3', 'mp3');
    $this->assertNotNull($this->renderer->render($match));
});

test('renders an image', function () {
    $match = new EmbedParserMatch('image.gif', 'gif');
    $this->assertNotNull($this->renderer->render($match));
});

test('renders Markdown', function () {
    $match = new EmbedParserMatch('markdown.md', 'md');
    $this->assertNotNull($this->renderer->render($match));
});

test('renders a PDF', function () {
    $match = new EmbedParserMatch('pdf.pdf', 'pdf');
    $this->assertNotNull($this->renderer->render($match));
});

test('renders video', function () {
    $match = new EmbedParserMatch('video.mp4', 'mp4');
    $this->assertNotNull($this->renderer->render($match));
});
