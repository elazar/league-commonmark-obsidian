<?php

namespace Elazar\LeagueCommonMarkObsidian;

use Elazar\LeagueCommonMarkObsidian\Formatter\FormatterInterface;
use Elazar\LeagueCommonMarkObsidian\Formatter\IdentityFormatter;
use Elazar\LeagueCommonMarkObsidian\Iterator\FileIterator;
use Elazar\LeagueCommonMarkObsidian\Iterator\MarkdownFileIterator;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Converter
{
    public function convert(
        string $vaultPath,
        string $attachmentsPath,
        string $buildPath,
        ?Environment $environment = null,
        ?FormatterInterface $formatter = null,
    ) {
        $vaultPath = $this->normalizePath($vaultPath);
        $attachmentsPath = $this->normalizePath($attachmentsPath);
        $buildPath = $this->normalizePath($buildPath);

        $environment ??= $this->getDefaultEnvironment();
        $formatter ??= $this->getDefaultFormatter();
        $buildAttachmentsPath = $this->getBuildAttachmentsPath($buildPath, $attachmentsPath);
        $extension = $this->getExtension($vaultPath, $attachmentsPath, $environment);

        $this->createDirectory($buildPath);
        $this->createDirectory($buildAttachmentsPath);
        $this->convertMarkdownFiles($environment, $extension, $formatter, $vaultPath, $buildPath);
        $this->copyAttachments($attachmentsPath, $buildAttachmentsPath);
    }

    protected function getDefaultEnvironment(): Environment
    {
        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension);
        return $environment;
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new IdentityFormatter;
    }

    protected function getExtension(
        string $vaultPath,
        string $attachmentsPath,
        Environment $environment,
    ): LeagueCommonMarkObsidianExtension {
        $extensions = iterator_to_array($environment->getExtensions());
        foreach ($extensions as $extension) {
            if ($extension instanceof LeagueCommonMarkObsidianExtension) {
                return $extension;
            }
        }

        $extension = new LeagueCommonMarkObsidianExtension(
            $vaultPath,
            $attachmentsPath,
        );
        $environment->addExtension($extension);
        return $extension;
    }

    protected function getBuildAttachmentsPath(
        string $buildPath,
        string $attachmentsPath,
    ): string {
        return $buildPath . basename($attachmentsPath);
    }

    protected function getBuildFilePath(
        string $vaultPath,
        string $filePath,
        string $buildPath,
    ): string {
        return $buildPath . str_replace(
            [$vaultPath, '.md'],
            ['', '.html'],
            $filePath,
        );
    }

    protected function getBuildAttachmentFilePath(
        string $attachmentsPath,
        string $filePath,
        string $buildAttachmentsPath,
    ): string {
        return $buildAttachmentsPath . str_replace(
            $attachmentsPath,
            DIRECTORY_SEPARATOR,
            $filePath,
        );
    }

    protected function normalizePath(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    protected function createDirectory(string $path): void
    {
        if (!file_exists($path)) {
            mkdir(directory: $path, recursive: true);
        }
    }

    protected function readFile(string $path): string
    {
        return file_get_contents($path);
    }

    protected function writeFile(string $path, string $contents): void
    {
        $directory = dirname($path);
        $this->createDirectory($directory);
        file_put_contents($path, $contents);
    }

    protected function copyFile(string $from, string $to): void
    {
        $toDirectory = dirname($to);
        $this->createDirectory($toDirectory);
        copy($from, $to);
    }

    protected function convertMarkdownFiles(
        Environment $environment,
        LeagueCommonMarkObsidianExtension $extension,
        FormatterInterface $formatter,
        string $vaultPath,
        string $buildPath,
    ): void {
        $converter = new MarkdownConverter($environment);
        $markdownFiles = new MarkdownFileIterator($vaultPath);
        foreach ($markdownFiles as $markdownFile) {
            $markdownFilePath = $markdownFile->getPathname();
            $extension->setFromPath($markdownFilePath);
            $markdownFileContents = $this->readFile($markdownFilePath);
            $htmlFilePath = $this->getBuildFilePath($vaultPath, $markdownFilePath, $buildPath);
            $convertedFileContents = $converter->convert($markdownFileContents);
            $formattedFileContents = $formatter->format($convertedFileContents, $markdownFilePath);
            $this->writeFile($htmlFilePath, $formattedFileContents);
        }
    }

    protected function copyAttachments(
        string $attachmentsPath,
        string $buildAttachmentsPath,
    ): void {
        $attachmentFiles = new FileIterator($attachmentsPath);
        foreach ($attachmentFiles as $attachmentFile) {
            $attachmentFilePath = $attachmentFile->getPathname();
            $buildAttachmentFilePath = $this->getBuildAttachmentFilePath(
                $attachmentsPath,
                $attachmentFilePath, 
                $buildAttachmentsPath,
            );
            $this->copyFile($attachmentFilePath, $buildAttachmentFilePath);
        }
    }
}
