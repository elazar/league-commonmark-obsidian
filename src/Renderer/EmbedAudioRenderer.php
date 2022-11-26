<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

class EmbedAudioRenderer implements EmbedRendererInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $attachmentLinkResolver,
    ) { }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        if (!$this->isAudioExtension($match->embedExtension)) {
            return null;
        }
        $attributes = [
            'controls' => true,
            'src' => $this->attachmentLinkResolver->resolve($match->embedName, $this->fromPath),
        ];
        return new HtmlElement('audio', $attributes);
    }

    /**
     * @return string[]
     */
    public static function getAudioExtensions(): array
    {
        return [
            'mp3',
            'webm',
            'wav',
            'm4a',
            'ogg',
            '3gp',
            'flac',
        ];
    }

    protected function isAudioExtension(string $extension): bool
    {
        return in_array($extension, static::getAudioExtensions());
    }
}
