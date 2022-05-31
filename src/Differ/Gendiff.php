<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatters\formatterSelection;

use const Differ\Formatters\STYLISH;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = STYLISH): string
{
    $firstArray = pathToArray($pathToFile1);
    $secondArray = pathToArray($pathToFile2);

    $replacedArray = array_replace_recursive($firstArray, $secondArray);
    $sortReplacedArray = sortRecursive($replacedArray);

    $ast = makeAst($sortReplacedArray, $firstArray, $secondArray);

    return formatterSelection($formatName, $ast);
}

function makeAst(array $replacedArray, array $arr1, array $arr2): array
{
    $iter = function ($currentValue, $check = false, $partArr1 = [], $partArr2 = []) use (&$iter) {
        if (!is_array($currentValue)) {
            return $currentValue;
        }

        return array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $check, $currentValue, $partArr1, $partArr2) {
                $culVal = $currentValue[$key];

                if ($check === false) {
                    $child = [
                        "type" => makeType($culVal),
                        "key" => $key,
                        "value" => $iter($culVal),
                    ];

                    return array_merge($acc, [$child]);
                }

                $inArr1 = is_array($partArr1) && array_key_exists($key, $partArr1);
                $inArr2 = is_array($partArr2) && array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    $value = $iter($culVal);
                    $operation = "added";
                } elseif (!$inArr2) {
                    $value = $iter($culVal);
                    $operation = "deleted";
                } elseif (is_array($culVal) && is_array($partArr1[$key])) {
                    $value = $iter($culVal, true, $partArr1[$key], $partArr2[$key]);
                    $operation = "not_changed";
                } elseif ($partArr1[$key] !== $partArr2[$key]) {
                    $child = [
                        "operation" => "changed",
                        "oldType" => makeType($partArr1[$key]),
                        "newType" => makeType($partArr2[$key]),
                        "key" => $key,
                        "oldValue" => $iter($partArr1[$key]),
                        "newValue" => $iter($partArr2[$key]),
                    ];

                    return array_merge($acc, [$child]);
                } else {
                    $value = $culVal;
                    $operation = "not_changed";
                }

                $child = [
                    "operation" => $operation,
                    "type" => makeType($culVal),
                    "key" => $key,
                    "value" => $value,
                ];

                return array_merge($acc, [$child]);
            },
            []
        );
    };
    $result = $iter($replacedArray, true, $arr1, $arr2);

    return $result;
}

function makeType($value): string
{
    return is_array($value) ? 'object' : 'simple';
}

function pathToArray(string $path): array
{
    $fileContent = file_get_contents($path);

    if ($fileContent === false) {
        return [];
    }

    [$fileName, $type] = explode('.', $path);

    return parse($fileContent, $type);
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
