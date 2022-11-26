<?php

namespace Elazar\LeagueCommonMarkObsidian\Resolver;

use Elazar\LeagueCommonMarkObsidian\Iterator\FileIterator;
use Elazar\LeagueCommonMarkObsidian\RelativePathTrait;

class AttachmentLinkResolver implements LinkResolverInterface
{
    use RelativePathTrait;

    private string $vaultPath;

    private \Traversable $iterator;

    /**
     * @var array<string, string>
     */
    private array $cache = [];

    public function __construct(
        string $vaultPath,
        string $attachmentsPath,
    ) {
        $this->iterator = new FileIterator($attachmentsPath);
        $this->vaultPath = realpath($vaultPath) . DIRECTORY_SEPARATOR;
    }

    public function resolve(string $name, string $fromPath): string
    {
        if (isset($this->cache[$name])) {
            $toPath = $this->cache[$name];
        } else {
            foreach ($this->iterator as $entry) {
                if ($entry->getFilename() === $name) {
                    $toPath = $this->cache[$name] = str_replace($this->vaultPath, '', $entry->getPathname());
                    break;
                }
            }
            if (!isset($toPath)) {
                throw LinkResolverException::failedToResolve($name);
            }
        }

        $relativeFromPath = str_replace($this->vaultPath, '', $fromPath);
        $resolvedPath = $this->getRelativePath($relativeFromPath, $toPath);
        return $resolvedPath;
    }
}