<?php

use Elazar\LeagueCommonMarkObsidian\FromPathTrait;

test('sets from path', function () {
    $instance = new class {
        use FromPathTrait;
    };
    $path = 'path/to/foo';
    $instance->setFromPath($path);
    $reflector = new \ReflectionProperty($instance, 'fromPath');
    $reflector->setAccessible(true);
    $this->assertSame($path, $reflector->getValue($instance));
});