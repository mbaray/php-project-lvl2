<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Formatters\formatterSelection;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish'): string
{
    $firstArray = pathToArray($pathToFile1);
    $secondArray = pathToArray($pathToFile2);

    $replacedArray = array_replace_recursive($firstArray, $secondArray);
    $sortReplacedArray = sortRecursive($replacedArray);

    return formatterSelection($formatName, $sortReplacedArray, $firstArray, $secondArray);
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function pathToArray(string $path): array
{
    $fileСontent = file_get_contents($path);
    [, $type] = explode('.', $path);

    return parse($fileСontent, $type);
}

function sortRecursive(array $arr): array
{
    ksort($arr);

    return array_map(fn($value) => is_array($value) ? sortRecursive($value) : $value, $arr);
}
