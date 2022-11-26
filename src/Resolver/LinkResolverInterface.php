<?php

namespace Elazar\LeagueCommonMarkObsidian\Resolver;

interface LinkResolverInterface
{
    /**
     * Resolve a given link name to a corresponding file path or URL.
     *
     * @param string $name Name / text of the link
     *        (e.g. in "[[[LinkName]]" the value for $name is "LinkName")
     * @param string $fromPath Path of the file containing the link, used
     *        to derive a relative path for the link
     * @return string Resolved file path or URL
     *
     * @throws LinkResolverException if resolution fails in an unrecoverable way
     */
    public function resolve(string $name, string $fromPath): string;
}
