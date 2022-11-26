<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;
use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParserMatch;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Util\HtmlElement;

class EmbedImageRenderer implements EmbedRendererInterface
{
    use FromPathTrait;

    public function __construct(
        private LinkResolverInterface $attachmentLinkResolver,
    ) { }

    public function render(EmbedParserMatch $match): ?HtmlElement
    {
        if (!$this->isImageExtension($match->embedExtension)) {
            return null;
        }
        $attributes = [
            'src' => $this->attachmentLinkResolver->resolve($match->embedName, $this->fromPath),
        ];
        if ($match->imageWidth !== null) {
            $attributes['width'] = (string) $match->imageWidth;
        }
        if ($match->imageHeight !== null) {
            $attributes['height'] = (string) $match->imageHeight;
        }
        return new HtmlElement('img', $attributes, '', true);
    }

    /**
     * @return string[]
     */
    public static function getImageExtensions(): array
    {
        return [
            'png',
            'jpg',
            'jpeg',
            'gif',
            'bmp',
            'svg',
        ];
    }

    protected function isImageExtension(string $extension): bool
    {
        return in_array($extension, self::getImageExtensions());
    }
}
