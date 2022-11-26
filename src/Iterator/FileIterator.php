<?php

namespace Elazar\LeagueCommonMarkObsidian\Iterator;

use Traversable;

class FileIterator implements \IteratorAggregate
{
    private \Traversable $iterator;

    public function __construct(string $path)
    {
        $this->iterator = new \CallbackFilterIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
            ),
            fn (\SplFileInfo $entry): bool => $entry->isFile(),
        );
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}