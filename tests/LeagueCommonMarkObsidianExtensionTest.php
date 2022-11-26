<?php

use Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianException;
use Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianExtension;
use Elazar\LeagueCommonmarkObsidian\LinkResolver\AttachmenLinkResolver;
use Elazar\LeagueCommonMarkObsidian\Resolver\AttachmentLinkResolver;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

beforeEach(function () {
    $this->vaultPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vault';
    mkdir($this->vaultPath);
    $this->internalLinkPath = $this->vaultPath . DIRECTORY_SEPARATOR . 'InternalLink.md';
    touch($this->internalLinkPath);
    $this->attachmentsPath = $this->vaultPath . DIRECTORY_SEPARATOR . 'Attachments';
    mkdir($this->attachmentsPath);
    $this->attachmentPath = $this->attachmentsPath . DIRECTORY_SEPARATOR . 'Attachment.gif';
    touch($this->attachmentPath);
});

afterEach(function () {
    unlink($this->attachmentPath);
    unlink($this->internalLinkPath);
    rmdir($this->attachmentsPath);
    rmdir($this->vaultPath);
});

test('parses and renders supported elements', function () {
    $extension = new LeagueCommonMarkObsidianExtension(
        $this->vaultPath,
        $this->attachmentsPath,
    );
    $environment = (new Environment)
        ->addExtension(new CommonMarkCoreExtension)
        ->addExtension($extension);
    $converter = new MarkdownConverter($environment);

    $extension->setFromPath($this->internalLinkPath);
    
    $actualInternalLink = (string) $converter->convert('[[InternalLink]]');
    $expectedInternalLink = "<p><a href=\"InternalLink.html\">InternalLink</a></p>\n";
    $this->assertSame($expectedInternalLink, $actualInternalLink);

    $actualAttachmentLink = (string) $converter->convert('![[Attachment.gif]]');
    $expectedAttachmentLink = "<p><img src=\"Attachments/Attachment.gif\" /></p>\n";
    $this->assertSame($expectedAttachmentLink, $actualAttachmentLink);
});

test('throws an error if required configuration is missing', function (array $args) {
    new LeagueCommonMarkObsidianExtension(...$args);
})->with(function () {
    return [
        'none' => [[]],
        'vaultPath only' => [['vaultPath' => 'path/to/vault']],
        'attachmentsPath only' => [['attachmentsPath' => 'path/to/attachments']],
        'attachmentLinkResolver only' => [['attachmentLinkResolver' => new AttachmentLinkResolver(__DIR__ . DIRECTORY_SEPARATOR . '..', __DIR__)]],
    ];
})->throws(LeagueCommonMarkObsidianException::class);