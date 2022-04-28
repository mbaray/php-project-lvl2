<?php

namespace Differ\Differ;

function genDiff(string $firstFile, string $secondFile): void
{
    $firstArray = convertToArray($firstFile);
    $secondArray = convertToArray($secondFile);

    $keys = array_merge(array_keys($firstArray), array_keys($secondArray));
    asort($keys);
    $uniqueKeys = array_unique($keys);

    $result = array_reduce($uniqueKeys, function ($acc, $key) use ($firstArray, $secondArray) {
        $first = array_key_exists($key, $firstArray) ? toString($firstArray[$key]) : null;
        $second = array_key_exists($key, $secondArray) ? toString($secondArray[$key]) : null;

        if ($first === $second) {
            $acc[] = getLine(' ', $key, $first);
        } elseif ($first && $second) {
            $acc[] = getLine('-', $key, $first);
            $acc[] = getLine('+', $key, $second);
        } elseif ($first) {
            $acc[] = getLine('-', $key, $first);
        } else {
            $acc[] = getLine('+', $key, $second);
        }

        return $acc;
    }, []);

    $resultString = implode("\n", $result);

    print_r("{\n{$resultString}\n}\n");
}

function getLine(string $symbol, string $key, string $value): string
{
    return "  {$symbol} {$key}: {$value}";
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function convertToArray(string $fileName): array
{
    $file = file_get_contents($fileName);

    return json_decode($file, true);
}
