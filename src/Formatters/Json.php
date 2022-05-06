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

                $culVal = $currentValue[$key];
                $name = $key;

                if ($check === false) {
                    $newAcc = [
                        "name" => $name,
                        "value" => $iter($culVal),
                    ];
                    $acc[] = $newAcc;

                    return $acc;
                }

                $inArr1 = is_array($partArr1) && array_key_exists($key, $partArr1);
                $inArr2 = is_array($partArr2) && array_key_exists($key, $partArr2);

                if (!$inArr1) {
                    $value = $iter($culVal);
                    $type = "added";
                } elseif (!$inArr2) {
                    $value = $iter($culVal);
                    $type = "deleted";
                } elseif (is_array($culVal) && is_array($partArr1[$key])) {
                    $value = $iter($culVal, true, $partArr1[$key], $partArr2[$key]);
                    $type = "unaltered";
                } elseif ($partArr1[$key] !== $partArr2[$key]) {
                    $acc[] = [
                        "name" => $name,
                        "oldValue" => $iter($partArr1[$key]),
                        "newValue" => $iter($partArr2[$key]),
                        "type" => "updated"
                    ];

                    return $acc;
                } else {
                    $value = $currentValue[$key];
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
