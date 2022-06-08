<?php

namespace Differ\Formatters\Plain;

use function Differ\String\toStringTxt;
use function Differ\Tree\isDeleted;
use function Differ\Tree\isChanged;
use function Differ\Tree\isAdded;
use function Differ\Tree\notChanged;
use function Differ\Tree\getKey;
use function Differ\Tree\getValue;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\isObject;

function format(array $ast): string
{
    $iter = function ($ast, $path) use (&$iter) {
        return array_reduce($ast, function ($acc, $node) use ($iter, $path) {
            $key = getKey($node);
            $newPath = ($path === '') ? "{$key}" : "{$path}.{$key}";

            if (notChanged($node) && isObject(getValue($node))) {
                return array_merge($acc, $iter(getValue($node), $newPath));
            }

            if (isAdded($node)) {
                $str = generateString(getValue($node));

                return array_merge($acc, ["Property '{$newPath}' was added with value: {$str}"]);
            }

            if (isDeleted($node)) {
                return array_merge($acc, ["Property '{$newPath}' was removed"]);
            }

            if (isChanged($node)) {
                $str1 = generateString(getOldValue($node));
                $str2 = generateString(getNewValue($node));

                return array_merge($acc, ["Property '{$newPath}' was updated. From {$str1} to {$str2}"]);
            }

            return $acc;
        }, []);
    };
    $result = $iter($ast, '');

    return implode("\n", $result);
}

function generateString(mixed $value): string
{
    return is_array($value) ? '[complex value]' : toStringTxt($value);
}
