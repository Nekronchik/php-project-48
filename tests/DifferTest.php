<?php

namespace Differ\tests\gendiffTests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

    class DifferTest extends TestCase
{
    public function testGendiff(): void
    {
        $path1 = __DIR__ . "/fixtures/before.json";
        $path2 = __DIR__ . '/fixtures/after.json';

        $test1 = genDiff($path1, $path2, 'json');
        $this->assertEquals($test1, genDiff($path1, $path2, 'json'));

        $test2 = genDiff($path1, $path2, 'plain');
        $this->assertEquals($test2, genDiff($path1, $path2, 'plain'));

        $test3 = genDiff($path1, $path2, 'stylish');
        $this->assertEquals($test3, genDiff($path1, $path2, 'stylish'));

        $test4 = genDiff($path1, $path2);
        $this->assertEquals($test4, genDiff($path1, $path2));
    }
}