<?php

namespace Elazar\LeagueCommonMarkObsidian;

class LeagueCommonMarkObsidianException extends \RuntimeException
{
    const CODE_FAILED_TO_CONFIGURE_EXTENSION = 1;

    public static function failedToConfigureExtension(string $message): self
    {
        return new self(
            'Failed to configure extension: ' . $message,
            self::CODE_FAILED_TO_CONFIGURE_EXTENSION,
        );
    }
}
