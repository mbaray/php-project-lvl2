<?php

namespace Differ\Formatters\Stylish;

use function Differ\String\toString;
use function Differ\Tree\isDeleted;
use function Differ\Tree\isChanged;
use function Differ\Tree\notChanged;
use function Differ\Tree\getKey;

function format(array $ast): string
{
    $iter = function (array $ast, int $depth = 1) use (&$iter) {
        $replacer = ' ';
        $spacesCount = 4;
        $indentSize = ($depth - 1) * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_reduce($ast, function ($acc, $arr) use ($iter, $depth, $indent) {
            $getLine = function ($curVal, $symbol) use ($indent, $arr, $iter, $depth) {
                $key = getKey($arr);
                if (!is_array($curVal)) {
                    $str = toString($curVal);

                    return "{$indent}  {$symbol} {$key}: {$str}";
                }
                return "{$indent}  {$symbol} {$key}: {$iter($curVal, $depth + 1)}";
            };

            if (notChanged($arr)) {
                return array_merge($acc, [$getLine($arr['value'], ' ')]);
            }

            if (isDeleted($arr)) {
                return array_merge($acc, [$getLine($arr['value'], '-')]);
            }

            if (isChanged($arr)) {
                return array_merge($acc, [$getLine($arr['oldValue'], '-')], [$getLine($arr['newValue'], '+')]);
            }

            return array_merge($acc, [$getLine($arr['value'], '+')]);
        }, []);
        $arrayLines = ['{', ...$lines, "{$indent}}"];

        return implode("\n", $arrayLines);
    };

    return $iter($ast);
}
