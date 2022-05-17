<?php

namespace Differ\Differ;

use function Functional\sort;
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

function toString(mixed $value): string
{
    $exportString = var_export($value, true);

    return is_string($value) ? $value : trim(mb_strtolower($exportString), "'");
}

function pathToArray(string $path): array
{
    $fileСontent = file_get_contents($path);

    if ($fileСontent === false || substr_count($path, '.') !== 1) {
        return [];
    }

    [, $type] = explode('.', $path);

    return parse($fileСontent, $type);
}

function sortRecursive(array $arr): array
{
    $sortArr = sort(
        $arr,
        fn($left, $right) => strcmp((string)array_search($left, $arr, true), (string)array_search($right, $arr, true)),
        true
    );

    return array_map(fn($value) => is_array($value) ? sortRecursive($value) : $value, $sortArr);
}
