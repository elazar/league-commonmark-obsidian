<?php

namespace Elazar\LeagueCommonMarkObsidian\Resolver;

use Elazar\LeagueCommonMarkObsidian\RelativePathTrait;
use Elazar\LeagueCommonMarkObsidian\Iterator\MarkdownFileIterator;

class InternalLinkResolver implements LinkResolverInterface
{
    use RelativePathTrait;

    private \Traversable $iterator;

    /**
     * @var array<string, string>
     */
    private array $cache = [];

    public function __construct(
        private string $vaultPath,
    ) {
        $this->iterator = new MarkdownFileIterator($vaultPath);
    }

    public function resolve(string $name, string $fromPath): string
    {
        if (isset($this->cache[$name])) {
            $toPath = $this->cache[$name];
        } else {
            $filename = $name . '.md';
            foreach ($this->iterator as $entry) {
                if ($entry->getFilename() === $filename) {
                    $toPath = $this->cache[$name] = str_replace(
                        [$this->vaultPath, '.md'],
                        ['', '.html'],
                        $entry->getPathname(),
                    );
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