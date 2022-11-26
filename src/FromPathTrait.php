<?php

namespace Elazar\LeagueCommonMarkObsidian;

trait FromPathTrait
{
    private string $fromPath = '';

    public function setFromPath(string $fromPath): void
    {
        $this->fromPath = $fromPath;
    }
}