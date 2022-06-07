<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\getParser;
use function Differ\Formatters\getFormatter;

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
        if (!is_array($partArr2)) {
            return $partArr2;
        }

        $replacedArray = array_replace($partArr1, $partArr2);
        $currentValue = sortByKeys($replacedArray);

        return array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use ($iter, $check, $currentValue, $partArr1, $partArr2) {
                $culVal = $currentValue[$key];

                if ($check === false) {
                    $child = [
                        "type" => makeType($culVal),
                        "key" => $key,
                        "value" => $iter([], $culVal),
                    ];

                    return array_merge($acc, [$child]);
                }

                $inArr1 = array_key_exists($key, $partArr1);
                $inArr2 = array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    $value = $iter([], $partArr2[$key]);
                    $operation = "added";
                } elseif (!$inArr2) {
                    $value = $iter([], $partArr1[$key]);
                    $operation = "deleted";
                } elseif (is_array($partArr2[$key]) && is_array($partArr1[$key])) {
                    $value = $iter($partArr1[$key], $partArr2[$key], true);
                    $operation = "not_changed";
                } elseif ($partArr1[$key] !== $partArr2[$key]) {
                    $child = [
                        "operation" => "changed",
                        "oldType" => makeType($partArr1[$key]),
                        "newType" => makeType($partArr2[$key]),
                        "key" => $key,
                        "oldValue" => $iter([], $partArr1[$key]),
                        "newValue" => $iter([], $partArr2[$key]),
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

    return $iter($arr1, $arr2, true);
}

function makeType(mixed $value): string
{
    return is_array($value) ? 'object' : 'simple';
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

function sortByKeys(array $arr): array
{
    return sort(
        $arr,
        fn($left, $right) => strcmp((string)array_search($left, $arr, true), (string)array_search($right, $arr, true)),
        true
    );
}
