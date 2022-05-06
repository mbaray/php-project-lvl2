<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\toString;

function formatting(array $replacedArray, array $arr1, array $arr2): string
{
    $iter = function ($currentValue, $depth, $check = false, $partArr1 = [], $partArr2 = []) use (&$iter) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $replacer = ' ';
        $spacesCount = 4;
        $indentSize = ($depth - 1) * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $depth, $check, $indent, $currentValue, $partArr1, $partArr2) {
                $value = $currentValue[$key];
                $getLine = fn($curVal, $symbol) => "{$indent}  {$symbol} {$key}: {$iter($curVal, ++$depth)}";

                if ($check === false) {
                    $acc[] = $getLine($value, ' ');
                    return $acc;
                }

                $inArr1 = array_key_exists($key, $partArr1);
                $inArr2 = array_key_exists($key, $partArr2);

                if (is_array($value)) {
                    if (!$inArr1) {
                        $acc[] = $getLine($value, '+');
                    } elseif (!$inArr2) {
                        $acc[] = $getLine($value, '-');
                    } else {
                        $value = $iter($value, ++$depth, true, $partArr1[$key], $partArr2[$key]);
                        $acc[] = "{$indent}    {$key}: {$value}";
                    }
                } elseif (($inArr1 && $inArr2) && ($partArr1[$key] !== $partArr2[$key])) {
                    $acc[] = $getLine($partArr1[$key], '-');
                    $acc[] = $getLine($partArr2[$key], '+');
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

    return $iter($replacedArray, 1, true, $arr1, $arr2) . "\n";
}
