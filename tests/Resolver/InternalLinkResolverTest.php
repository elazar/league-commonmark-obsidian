<?php

use Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianException;
use Elazar\LeagueCommonMarkObsidian\Resolver\InternalLinkResolver;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverException;

test('does not resolve if attachment does not exist', function () {
    $resolver = new InternalLinkResolver(__DIR__);
    $resolver->resolve('DoesNotExist', __FILE__);
})->throws(LinkResolverException::class);

test('resolves if attachment exists', function () {
    $vaultPath = sys_get_temp_dir();
    $linkName = 'LinkName';
    $fileName = $linkName . '.md';
    $filePath = $vaultPath . DIRECTORY_SEPARATOR . $fileName;
    if (!touch($filePath)) {
        return $this->markTestSkipped('Unable to write to ' . $filePath);
    }
    $resolver = new InternalLinkResolver($vaultPath);
    $resolvedFileName = $linkName . '.html';
    $this->assertSame($resolvedFileName, $resolver->resolve($linkName, $filePath));
    $this->assertSame($resolvedFileName, $resolver->resolve($linkName, $filePath)); // Test caching
    unlink($filePath);
});
