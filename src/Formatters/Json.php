<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\toString;

function formatting(array $replacedArray, array $arr1, array $arr2): string
{
    $iter = function ($currentValue, $check = false, $partArr1 = [], $partArr2 = []) use (&$iter) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        return array_reduce(
            array_keys($currentValue),
            function ($acc, $key) use (&$iter, $check, $currentValue, $partArr1, $partArr2) {

                $value = $currentValue[$key];
                $name = $key;

                if ($check === false) {
                    $acc[] = [
                        "name" => $name,
                        "value" => $iter($value),
                    ];

                    return $acc;
                }

                $inArr1 = is_array($partArr1) && array_key_exists($key, $partArr1);
                $inArr2 = is_array($partArr2) && array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    $value = $iter($value);
                    $type = "added";
                } elseif (!$inArr2) {
                    $value = $iter($value);
                    $type = "deleted";
                } elseif (is_array($value)) {
                    $value = $iter($value, true, $partArr1[$key], $partArr2[$key]);
                    $type = "unaltered";
                } elseif ($partArr1[$key] !== $partArr2[$key]) {
                    $type = "updated";
                    $acc[] = [
                        "name" => $name,
                        "oldValue" => $iter($partArr1[$key]),
                        "newValue" => $iter($partArr2[$key]),
                        "type" => $type
                    ];

                    return $acc;
                } else {
                    $type = "unaltered";
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
    $result = $iter($replacedArray, true, $arr1, $arr2);

    return json_encode($result);
}
