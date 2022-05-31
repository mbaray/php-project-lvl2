<?php

namespace Differ\Formatters\Json;

use function Differ\String\toString;

function formatting(array $ast): string
{
    $iter = function (array $ast) use (&$iter) {
        return array_map(function ($arr) use ($iter) {
            if (!array_key_exists('operation', $arr)) {
                return [
                    "name" => $arr['key'],
                    "value" => $arr['type'] === 'object' ? $iter($arr['value']) : toString($arr['value']),
                ];
            }

            if ($arr['operation'] === 'changed') {
                return [
                    "name" => $arr['key'],
                    "oldValue" => $arr['oldType'] === 'simple' ? toString($arr['oldValue']) : $iter($arr['oldValue']),
                    "newValue" => $arr['newType'] === 'simple' ? toString($arr['newValue']) : $iter($arr['newValue']),
                    "type" => 'updated',
                ];
            }

            return [
                "name" => $arr['key'],
                "value" => $arr['type'] === 'object' ? $iter($arr['value']) : toString($arr['value']),
                "type" => $arr['operation'] === 'not_changed' ? 'unaltered' : $arr['operation'],
            ];
        }, $ast);
    };

    $result = $iter($ast);

    return json_encode($result);
}
