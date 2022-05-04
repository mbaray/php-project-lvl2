<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiffJson()
    {
        $actual = genDiff('tests/fixtures/json/file1.json', 'tests/fixtures/json/file2.json', 'stylish');
        $expected = file_get_contents('./tests/fixtures/expected.txt');
        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffYaml()
    {
        $actual = genDiff('tests/fixtures/yaml/file1.yml', 'tests/fixtures/yaml/file2.yml', 'stylish');
        $expected = file_get_contents('./tests/fixtures/expected.txt');
        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffWrongFormat()
    {
        $actual = genDiff('./tests/fixtures/expected.txt', './tests/fixtures/expected.txt', 'stylish');
        $this->assertEquals("{\n}\n", $actual);
    }
}
