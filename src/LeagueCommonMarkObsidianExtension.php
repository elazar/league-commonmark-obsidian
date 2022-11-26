<?php

namespace Elazar\LeagueCommonMarkObsidian;

use Elazar\LeagueCommonMarkObsidian\Parser\EmbedParser;
use Elazar\LeagueCommonMarkObsidian\Parser\InternalLinkParser;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRenderer;
use Elazar\LeagueCommonMarkObsidian\Renderer\EmbedRendererInterface;
use Elazar\LeagueCommonMarkObsidian\Resolver\AttachmentLinkResolver;
use Elazar\LeagueCommonMarkObsidian\Resolver\InternalLinkResolver;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;

class LeagueCommonMarkObsidianExtension implements ExtensionInterface
{
    private EmbedRendererInterface $embedRenderer;

    private EmbedParser $embedParser;

    private InternalLinkParser $internalLinkParser;

    public function __construct(
        ?string $vaultPath = null,
        ?string $attachmentsPath = null,
        ?EmbedRendererInterface $embedRenderer = null,
        ?LinkResolverInterface $attachmentLinkResolver = null,
        ?LinkResolverInterface $internalLinkResolver = null,
    ) {
        if (!$attachmentLinkResolver && !($vaultPath && $attachmentsPath)) {
            throw LeagueCommonMarkObsidianException::failedToConfigureExtension(
                '$vaultPath and $attachments are required if $attachmentLinkResolver is not specified',
            );
        }
        if (!$internalLinkResolver && !$vaultPath) {
            throw LeagueCommonMarkObsidianException::failedToConfigureExtension(
                '$vaultPath is required if $internalLinkResolver is not specified',
            );
        }

        $attachmentLinkResolver ??= new AttachmentLinkResolver($vaultPath, $attachmentsPath);
        $internalLinkResolver ??= new InternalLinkResolver($vaultPath);
        $this->embedRenderer = $embedRenderer ?? new EmbedRenderer($internalLinkResolver, $attachmentLinkResolver);
        $this->embedParser ??= new EmbedParser($this->embedRenderer);
        $this->internalLinkParser ??= new InternalLinkParser($internalLinkResolver);
    }

    /**
     * Convenience method to pass the path of the Markdown file being parsed
     * to the renderer so that links can be resolved relative to that file.
     */
    public function setFromPath(string $fromPath): void
    {
        $this->embedRenderer->setFromPath($fromPath);
        $this->internalLinkParser->setFromPath($fromPath);
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addInlineParser($this->embedParser, 100)
            ->addInlineParser($this->internalLinkParser, 100);
    }
}