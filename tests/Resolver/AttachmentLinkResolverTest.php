<?php

use Elazar\LeagueCommonMarkObsidian\Resolver\AttachmentLinkResolver;
use Elazar\LeagueCommonMarkObsidian\Resolver\LinkResolverException;

test('does not resolve if attachment does not exist', function () {
    $resolver = new AttachmentLinkResolver(__DIR__ . '/..', __DIR__);
    $resolver->resolve('does-not-exist.md', 'Pest.php');
})->throws(LinkResolverException::class);

test('resolves if attachment exists', function () {
    $resolver = new AttachmentLinkResolver(__DIR__ . '/..', __DIR__);
    $name = 'InternalLinkResolverTest.php';
    $fromPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Pest.php';
    $resolved = 'Resolver/' . $name;
    $this->assertSame($resolved, $resolver->resolve($name, $fromPath));
    $this->assertSame($resolved, $resolver->resolve($name, $fromPath)); // Test caching
});