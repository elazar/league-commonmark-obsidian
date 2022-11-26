<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

class EmbedVideoRenderer implements EmbedRendererInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $attachmentLinkResolver,
    ) { }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        $type = static::getVideoType($match->embedExtension);
        if ($type === null) {
            return null;
        }
        $source = new HtmlElement('source', [
            'src' => $this->attachmentLinkResolver->resolve($match->embedName, $this->fromPath),
            'type' => $type,
        ]);
        return new HtmlElement('video', ['controls' => true], $source);
    }

    /**
     * @return string[]
     */
    public static function getVideoExtensions(): array
    {
        return [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogv' => 'video/ogg',
            'mov' => 'video/quicktime',
            'mkv' => 'video/mp4',
        ];
    }

    private static function getVideoType(string $extension): ?string
    {
        return static::getVideoExtensions()[$extension] ?? null;
    }
}
