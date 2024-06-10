<?php

namespace Differ\tests\gendiffTests;

use PHPUnit\Framework\TestCase;

use function Differ\src\Differ\genDiff;

class DiffTest extends TestCase {
    public function testGendiff(): void
    {
        $result = genDiff();	
    }
}