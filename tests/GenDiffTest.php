<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $actual = genDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json');
        $expected = file_get_contents('./tests/fixtures/expected.txt');
        $this->assertEquals($expected, $actual);
    }
}
