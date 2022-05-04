<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\toString;

function stylish(array $keys, array $arr1, array $arr2): string
{
    $iter = function ($currentValue, $depth, $check = false, $part1 = [], $part2 = []) use (&$iter) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $replacer = ' ';
        $spacesCount = 4;

        $indentSize = ($depth - 1) * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $depth, $check, $indent, $currentValue, $part1, $part2) {

                $value = $currentValue[$key];

                $getLine = fn($curVal, $symbol) => "{$indent}  {$symbol} {$key}: {$iter($curVal, ++$depth)}";


                if ($check === false) {
                    $acc[] = $getLine($value, ' ');
                    return $acc;
                }

                $inArr1 = array_key_exists($key, $part1);
                $inArr2 = array_key_exists($key, $part2);

                if (is_array($value)) {
                    if (!$inArr1) {
                        $acc[] = $getLine($value, '+');
                    } elseif (!$inArr2) {
                        $acc[] = $getLine($value, '-');
                    } else {
                        $acc[] = "{$indent}    {$key}: {$iter($value, ++$depth, true, $part1[$key], $part2[$key])}";
                        // $acc[] = $getLine(' ', true, $partArr1[$key], $partArr2[$key]);
                    }
                } elseif (($inArr1 && $inArr2) && ($part1[$key] !== $part2[$key])) {
                    $acc[] = $getLine($part1[$key], '-');
                    $acc[] = $getLine($part2[$key], '+');
                } else {
                    if (!$inArr1) {
                        $acc[] = $getLine($value, '+');
                    } elseif (!$inArr2) {
                        $acc[] = $getLine($value, '-');
                    } else {
                        $acc[] = $getLine($value, ' ');
                    }
                }

                return $acc;
            },
            []
        );

        $result = ['{', ...$lines, "{$indent}}"];

        return implode("\n", $result);
    };

    return $iter($keys, 1, true, $arr1, $arr2) . "\n";
}
