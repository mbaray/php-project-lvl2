<?php

namespace Differ\Formatters;

const STYLISH = 'stylish';
const PLAIN = 'plain';
const JSON = 'json';

function formatterSelection(string $formatName, array $ast): string
{
    /** @var callable $getFormatting */
    $getFormatting = "\\Differ\\Formatters\\{$formatName}\\formatting";

    return $getFormatting($ast);
}
