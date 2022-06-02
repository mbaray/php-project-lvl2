<?php

namespace Differ\Formatters;

const STYLISH = 'stylish';
const PLAIN = 'plain';
const JSON = 'json';

function getFormatter(string $format): callable
{
    /** @var callable $getFormatter */
    $getFormatter = "\\Differ\\Formatters\\{$format}\\format";

    return $getFormatter;
}
