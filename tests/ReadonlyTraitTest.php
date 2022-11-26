<?php

use Elazar\LeagueCommonMarkObsidian\ReadonlyTrait;
use Whoops\Exception\ErrorException;

test('returns an existing property', function () {
    $instance = new class {
        use ReadonlyTrait;
        private string $property = 'test';
    };
    $this->assertSame('test', $instance->property);
});

test('triggers an error on an nonexistent property', function () {
    $instance = new class {
        use ReadonlyTrait;
    };
    echo $instance->property;
})->throws(ErrorException::class);