<?php

namespace Formatters;

function formatterSelection(string $formatName, array $replacedArray, array $arr1, array $arr2): string
{
    /** @var callable $getFormatting */
    $getFormatting = "\\Differ\\Formatters\\{$formatName}\\formatting";

    return $getFormatting($replacedArray, $arr1, $arr2);
}
