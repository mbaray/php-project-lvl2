<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Formatters\formatterSelection;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish'): string
{
    $firstArray = toArray($pathToFile1);
    $secondArray = toArray($pathToFile2);

    $keys = array_replace_recursive($firstArray, $secondArray);
    $sortKeys = sortRecursive($keys);

    return formatterSelection($formatName, $sortKeys, $firstArray, $secondArray);
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function toArray(string $path): array
{
    $fileСontent = file_get_contents($path);
    [, $type] = explode('.', $path);

    return parse($fileСontent, $type);
}

function sortRecursive(array $arr): array
{
    if (is_array($arr)) {
        ksort($arr);
    }

    return array_map(fn($value) => is_array($value) ? sortRecursive($value) : $value, $arr);
}
