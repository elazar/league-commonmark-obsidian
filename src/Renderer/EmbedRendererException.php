<?php

namespace Elazar\LeagueCommonMarkObsidian\Renderer;

use Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianException;

class EmbedRendererException extends LeagueCommonMarkObsidianException
{
    const CODE_FAILED_TO_RENDER = 1;

    public static function failedToRender(string $name): static
    {
        return new static(
            'Unable to render parsed embed: ' . $name,
            static::CODE_FAILED_TO_RENDER,
        );
    }
}
