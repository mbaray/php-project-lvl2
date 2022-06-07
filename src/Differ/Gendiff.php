<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\getParser;
use function Differ\Formatters\getFormatter;
use function Differ\Tree\makeNode;

use const Differ\Formatters\STYLISH;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = STYLISH): string
{
    $firstArray = getContent($pathToFile1);
    $secondArray = getContent($pathToFile2);

    $ast = makeAst($firstArray, $secondArray);
    $formatter = getFormatter($formatName);

    return $formatter($ast);
}

function makeAst(array $arr1, array $arr2): array
{
    $iter = function ($partArr1 = [], $partArr2 = [], $check = false) use (&$iter) {
        if (!is_array($partArr1)) {
            return $partArr1;
        }

        $mergedKeys = array_merge(array_keys($partArr1), array_keys($partArr2));
        $uniqueKeys = array_unique($mergedKeys);
        $keys = sort($uniqueKeys, fn($left, $right) => strcmp($left, $right));

        return array_reduce($keys, function ($acc, $key) use ($iter, $check, $partArr1, $partArr2) {
            if ($check === false) {
                $value = $iter($partArr1[$key]);
                $child = makeNode($key, $value);

                return array_merge($acc, [$child]);
            }

            $inArr1 = array_key_exists($key, $partArr1);
            $inArr2 = array_key_exists($key, $partArr2);

            if (!$inArr1) {
                $value = $iter($partArr2[$key]);
                $child = makeNode($key, $value, 'added');
            } elseif (!$inArr2) {
                $value = $iter($partArr1[$key]);
                $child = makeNode($key, $value, 'deleted');
            } elseif (is_array($partArr2[$key]) && is_array($partArr1[$key])) {
                $value = $iter($partArr1[$key], $partArr2[$key], true);
                $child = makeNode($key, $value, 'not_changed');
            } elseif ($partArr1[$key] !== $partArr2[$key]) {
                $value = [$iter($partArr1[$key]), $iter($partArr2[$key])];
                $child = makeNode($key, $value, 'changed');
            } else {
                $value = $partArr1[$key];
                $child = makeNode($key, $value, 'not_changed');
            }

            return array_merge($acc, [$child]);
        }, []);
    };

    return $iter($arr1, $arr2, true);
}

function getContent(string $path): array
{
    $fileContent = file_get_contents($path);

    if ($fileContent === false) {
        return [];
    }

    [$fileName, $type] = explode('.', $path);

    $parser = getParser($type);

    return $parser($fileContent);
}
