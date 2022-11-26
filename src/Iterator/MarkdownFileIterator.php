<?php

namespace Elazar\LeagueCommonMarkObsidian\Iterator;

use Traversable;

class MarkdownFileIterator implements \IteratorAggregate
{
    private Traversable $iterator;

    public function __construct(string $vaultPath)
    {
        $this->iterator = new \CallbackFilterIterator(
            (new FileIterator($vaultPath))->getIterator(),
            fn (\SplFileInfo $entry): bool => $entry->getExtension() === 'md',
        );
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}