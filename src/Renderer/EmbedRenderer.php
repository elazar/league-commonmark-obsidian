<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;

class EmbedRenderer extends CompositeEmbedRenderer
{
    public function __construct(
        private LinkResolverInterface $internalLinkResolver,
        private LinkResolverInterface $attachmentLinkResolver,
    ) {
        parent::__construct(
            new EmbedAudioRenderer($attachmentLinkResolver),
            new EmbedImageRenderer($attachmentLinkResolver),
            new EmbedMarkdownRenderer($internalLinkResolver),
            new EmbedPdfRenderer($attachmentLinkResolver),
            new EmbedVideoRenderer($attachmentLinkResolver),
        );
    }
}
