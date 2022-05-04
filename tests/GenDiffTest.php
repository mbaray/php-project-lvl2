<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiffJson()
    {
        $actualStylish = genDiff('tests/fixtures/json/file1.json', 'tests/fixtures/json/file2.json', 'stylish');
        $expectedStylish = file_get_contents('./tests/fixtures/expectedStylish.txt');
        $this->assertEquals($expectedStylish, $actualStylish);

        $actualPlain = genDiff('tests/fixtures/json/file1.json', 'tests/fixtures/json/file2.json', 'plain');
        $expectedPlain = file_get_contents('./tests/fixtures/expectedPlain.txt');
        $this->assertEquals($expectedPlain, $actualPlain);
    }

    public function testGenDiffYaml()
    {
        $actual = genDiff('tests/fixtures/yaml/file1.yml', 'tests/fixtures/yaml/file2.yml', 'stylish');
        $expected = file_get_contents('./tests/fixtures/expectedStylish.txt');
        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffWrongFormat()
    {
        $actual = genDiff('./tests/fixtures/expectedStylish.txt', './tests/fixtures/expectedStylish.txt', 'stylish');
        $this->assertEquals("{\n}\n", $actual);
    }
}
