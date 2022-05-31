<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $fileContent, string $type): array
{
    switch ($type) {
        case 'json':
            return json_decode($fileContent, true);

        case 'yml':
        case 'yaml':
            return toArrayRecursive(Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP));

        default:
            return [];
    }
}

function toArrayRecursive(object $obj): array
{
    $objVars = get_object_vars($obj);

    return array_map(fn($value) => is_object($value) ? toArrayRecursive($value) : $value, $objVars);
}
