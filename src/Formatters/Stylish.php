<?php

namespace Differ\Formatters\Stylish;

use function Differ\String\toString;

function format(array $ast): string
{
    $iter = function (array $ast, int $depth = 1) use (&$iter) {
        $replacer = ' ';
        $spacesCount = 4;
        $indentSize = ($depth - 1) * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_reduce($ast, function ($acc, $arr) use ($iter, $depth, $indent) {
            $getLine = function ($curVal, $symbol) use ($indent, $arr, $iter, $depth) {
                if (!is_array($curVal)) {
                    $str = toString($curVal);

                    return "{$indent}  {$symbol} {$arr['key']}: {$str}";
                }
                return "{$indent}  {$symbol} {$arr['key']}: {$iter($curVal, $depth + 1)}";
            };

            if (!array_key_exists('operation', $arr) || $arr['operation'] === 'not_changed') {
                return array_merge($acc, [$getLine($arr['value'], ' ')]);
            }

            if ($arr['operation'] === 'added') {
                return array_merge($acc, [$getLine($arr['value'], '+')]);
            }

            if ($arr['operation'] === 'deleted') {
                return array_merge($acc, [$getLine($arr['value'], '-')]);
            }

            if ($arr['operation'] === 'changed') {
                return array_merge($acc, [$getLine($arr['oldValue'], '-')], [$getLine($arr['newValue'], '+')]);
            }
        }, []);
        $arrayLines = ['{', ...$lines, "{$indent}}"];

        return implode("\n", $arrayLines);
    };

    return $iter($ast);
}
