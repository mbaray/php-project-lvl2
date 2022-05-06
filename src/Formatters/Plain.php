<?php

namespace Differ\Formatters\Plain;

use function Differ\Differ\toString;

function formatting(array $replacedArray, array $arr1, array $arr2): string
{
    $iter = function ($currentValue, $path, $partArr1 = [], $partArr2 = []) use (&$iter) {
        return array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $path, $currentValue, $partArr1, $partArr2) {
                $path = ($path === '') ? "{$key}" : "{$path}.{$key}";
                $value = $currentValue[$key];

                $inArr1 = array_key_exists($key, $partArr1);
                $inArr2 = array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    $str = is_array($value) ? '[complex value]' : toStringTxt($value);

                    $acc .= "Property '{$path}' was added with value: {$str}\n";
                } elseif (!$inArr2) {
                    $acc .= "Property '{$path}' was removed\n";
                } elseif (is_array($value)) {
                    $acc .= $iter($value, $path, $partArr1[$key], $partArr2[$key]);
                } elseif ($partArr1[$key] !== $partArr2[$key]) {
                    $str1 = is_array($partArr1[$key]) ? '[complex value]' : toStringTxt($partArr1[$key]);
                    $str2 = is_array($partArr2[$key]) ? '[complex value]' : toStringTxt($partArr2[$key]);

                    $acc .= "Property '{$path}' was updated. From {$str1} to {$str2}\n";
                }

                return $acc;
            },
            ''
        );
    };

    return $iter($replacedArray, '', $arr1, $arr2);
}

function toStringTxt($value): string
{
    $string = toString($value);

    return is_string($value) ? "'{$string}'" : $string;
}
