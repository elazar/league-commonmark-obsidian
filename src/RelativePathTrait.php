<?php

namespace Elazar\LeagueCommonMarkObsidian;

trait RelativePathTrait
{
    private function getRelativePath(string $from, string $to): string
    {
        $fromComponents = explode(DIRECTORY_SEPARATOR, $from);
        $toComponents = explode(DIRECTORY_SEPARATOR, $to);
        $lastToComponent = $toComponents[array_key_last($toComponents)];
        while (count($fromComponents) && count($toComponents) && $fromComponents[0] === $toComponents[0]) {
            array_shift($fromComponents);
            array_shift($toComponents);
        }
        $resolvedPath = empty($fromComponents)
            ? $lastToComponent
            : str_repeat('..' . DIRECTORY_SEPARATOR, count($fromComponents) - 1) . implode(DIRECTORY_SEPARATOR, $toComponents);
        return $resolvedPath;
    }
}