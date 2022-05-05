<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\toString;

function json(array $keys, array $arr1, array $arr2): string
{

    $iter = function ($currentValue, $check = false, $part1 = [], $part2 = []) use (&$iter) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        return array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $check, $currentValue, $part1, $part2) {

                $value = $currentValue[$key];
                $name = $key;

                if ($check === false) {
                    $acc[] = [
                        "name" => $name,
                        "value" => $iter($value),
                    ];

                    return $acc;
                }

                $inArr1 = array_key_exists($key, $part1);
                $inArr2 = array_key_exists($key, $part2);

                if (is_array($value)) {
                    if (!$inArr1) {
                        $value = $iter($value);
                        $type = "added";
                    } elseif (!$inArr2) {
                        $value = $iter($value);
                        $type = "deleted";
                    } else {
                        $value = $iter($value, true, $part1[$key], $part2[$key]);
                        $type = "unaltered";
                    }
                } elseif (($inArr1 && $inArr2) && ($part1[$key] !== $part2[$key])) {
                    $type = "updated";
                    $acc[] = [
                        "name" => $name,
                        "oldValue" => $iter($part1[$key]),
                        "newValue" => $iter($part2[$key]),
                        "type" => $type
                    ];

                    return $acc;
                } else {
                    $value = $iter($value);
                    if (!$inArr1) {
                        $type = "added";
                    } elseif (!$inArr2) {
                        $type = "deleted";
                    } else {
                        $type = "unaltered";
                    }
                }

                $acc[] = [
                    "name" => $name,
                    "value" => $value,
                    "type" => $type
                ];

                return $acc;
            },
            []
        );
    };

    $result = $iter($keys, true, $arr1, $arr2);

    return json_encode($result) . "\n";
}
