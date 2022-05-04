<?php

namespace Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;

function formatterSelection(string $formatName, array $keys, array $arr1, array $arr2): string
{
    switch ($formatName) {
        case 'stylish':
            return stylish($keys, $arr1, $arr2);

        case 'plain':
            return plain($keys, $arr1, $arr2);
    }
}
