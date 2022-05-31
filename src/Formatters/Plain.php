<?php

namespace Differ\Formatters\Plain;

use function Differ\String\toStringTxt;

function formatting(array $ast): string
{
    $iter = function ($ast, $path) use (&$iter) {
        return array_reduce($ast, function ($acc, $arr) use ($iter, $path) {
            $newPath = ($path === '') ? "{$arr['key']}" : "{$path}.{$arr['key']}";

            if ($arr['operation'] === 'not_changed') {
                if ($arr['type'] === 'object') {
                    return array_merge($acc, $iter($arr['value'], $newPath));
                }
            }

            if ($arr['operation'] === 'added') {
                $str = $arr['type'] === 'object' ? '[complex value]' : toStringTxt($arr['value']);

                return array_merge($acc, ["Property '{$newPath}' was added with value: {$str}"]);
            }

            if ($arr['operation'] === 'deleted') {
                return array_merge($acc, ["Property '{$newPath}' was removed"]);
            }

            if ($arr['operation'] === 'changed') {
                $str1 = $arr['oldType'] === 'object' ? '[complex value]' : toStringTxt($arr['oldValue']);
                $str2 = $arr['newType'] === 'object' ? '[complex value]' : toStringTxt($arr['newValue']);

                return array_merge($acc, ["Property '{$newPath}' was updated. From {$str1} to {$str2}"]);
            }

            return $acc;
        }, []);
    };
    $result = $iter($ast, '');

    return implode("\n", $result);
}
