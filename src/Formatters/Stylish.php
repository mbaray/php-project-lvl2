<?php

namespace Differ\Formatters\Stylish;

use function Differ\String\toString;
use function Differ\Tree\isDeleted;
use function Differ\Tree\isChanged;
use function Differ\Tree\notChanged;
use function Differ\Tree\getKey;
use function Differ\Tree\getValue;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\isObject;

function format(array $ast): string
{
    $iter = function (array $ast, int $depth = 1) use (&$iter) {
        $replacer = ' ';
        $spacesCount = 4;
        $indentSize = ($depth - 1) * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_reduce($ast, function ($acc, $node) use ($iter, $depth, $indent) {
            $getLine = function ($value, $symbol) use ($indent, $node, $iter, $depth) {
                $key = getKey($node);
                if (isObject($value)) {
                    return "{$indent}  {$symbol} {$key}: {$iter($value, $depth + 1)}";
                }
                $str = toString($value);

                return "{$indent}  {$symbol} {$key}: {$str}";
            };

            if (!array_key_exists('operation', $node) || notChanged($node)) {
                return array_merge($acc, [$getLine(getValue($node), ' ')]);
            }

            if (isDeleted($node)) {
                return array_merge($acc, [$getLine(getValue($node), '-')]);
            }

            if (isChanged($node)) {
                return array_merge($acc, [$getLine(getOldValue($node), '-')], [$getLine(getNewValue($node), '+')]);
            }

            return array_merge($acc, [$getLine(getValue($node), '+')]);
        }, []);
        $arrayLines = ['{', ...$lines, "{$indent}}"];

        return implode("\n", $arrayLines);
    };

    return $iter($ast);
}
