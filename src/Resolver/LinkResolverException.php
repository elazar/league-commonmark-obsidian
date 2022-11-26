<?php

namespace Elazar\LeagueCommonMarkObsidian\Resolver;

use Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianException;

class LinkResolverException extends LeagueCommonMarkObsidianException
{
    const CODE_FAILED_TO_RESOLVE = 1;

    public static function failedToResolve(string $name): static
    {
        return new static(
            'Unable to resolve link: ' . $name,
            static::CODE_FAILED_TO_RESOLVE,
        );
    }
}
