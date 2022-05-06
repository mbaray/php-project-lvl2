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
                $getLine = fn($curVal, $symbol) => "{$indent}  {$symbol} {$key}: {$iter($curVal, $depth + 1)}";

                if ($check === false) {
                    return array_merge($acc, [$getLine($value, ' ')]);
                }

                $inArr1 = is_array($partArr1) && array_key_exists($key, $partArr1);
                $inArr2 = is_array($partArr2) && array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    return array_merge($acc, [$getLine($value, '+')]);
                }

                if (!$inArr2) {
                    return array_merge($acc, [$getLine($value, '-')]);
                }

                if (is_array($value) && is_array($partArr1[$key])) {
                    $funcIter = $iter($value, $depth + 1, true, $partArr1[$key], $partArr2[$key]);

                    return array_merge($acc, ["{$indent}    {$key}: {$funcIter}"]);
                }

                if ($partArr1[$key] !== $partArr2[$key]) {
                    return array_merge($acc, [$getLine($partArr1[$key], '-')], [$getLine($partArr2[$key], '+')]);
                }

                return array_merge($acc, [$getLine($value, ' ')]);
            },
            []
        );
        $result = ['{', ...$lines, "{$indent}}"];

        return implode("\n", $result);
    };

    return $iter($replacedArray, 1, true, $arr1, $arr2);
}
