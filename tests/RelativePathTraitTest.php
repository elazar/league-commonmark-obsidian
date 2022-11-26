<?php

use Elazar\LeagueCommonMarkObsidian\RelativePathTrait;

test('resolves relative path', function (string $from, string $to, string $expected) {
    $instance = new class {
        use RelativePathTrait;
        public function resolve(string $from, string $to) {
            return $this->getRelativePath($from, $to);
        }
    };
    $actual = $instance->resolve($from, $to);
    $this->assertSame($expected, $actual);
})->with([
    ['/path/to/file.html', '/path/to/other.html', 'other.html'],
    ['/path/to/file.html', '/path/of/file.html', '../of/file.html'],
    ['/path/to/file.html', '/path/to/file.html', 'file.html'],
    ['/path/to/file.html', '/path/to/sub/file.html', 'sub/file.html'],
    ['/path/to/sub/file.html', '/path/to/file.html', '../file.html'],
]);