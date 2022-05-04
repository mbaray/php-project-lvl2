<?php

namespace Differ\Formatters\Plain;

function plain(array $keys, array $arr1, array $arr2): string
{
    $iter = function ($currentValue, $path, $part1 = [], $part2 = []) use (&$iter) {

        $result = array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $path, $currentValue, $part1, $part2) {
                $path = ($path === '') ? "$key" : "$path.$key";
                $value = $currentValue[$key];

                $inArr1 = array_key_exists($key, $part1);
                $inArr2 = array_key_exists($key, $part2);

                if (is_array($value)) {
                    if (!$inArr1) {
                        $acc .= "Property '{$path}' was added with value: [complex value]\n";
                    } elseif (!$inArr2) {
                        $acc .= "Property '{$path}' was removed\n";
                    } else {
                        $acc .= $iter($value, $path, $part1[$key], $part2[$key]);
                    }
                } elseif (($inArr1 && $inArr2) && ($part1[$key] !== $part2[$key])) {
                    $val1 = is_array($part1[$key]) ? '[complex value]': toString($part1[$key]);
                    $val2 = is_array($part2[$key]) ? '[complex value]': toString($part2[$key]);

                    $acc .= "Property '{$path}' was updated. From {$val1} to {$val2}\n";
                } else {
                    if (!$inArr1) {
                        $val = toString($value);
                        $acc .= "Property '{$path}' was added with value: {$val}\n";
                    } elseif (!$inArr2) {
                        $acc .= "Property '{$path}' was removed\n";
                    }
                }

                return $acc;
            },
            ''
        );

        return $result;
    };

    return $iter($keys, '', $arr1, $arr2);
}

function toString($value): string
{
    $string = trim(var_export($value, true), "'");

    return is_string($value) ? "'{$string}'" : $string;
}
