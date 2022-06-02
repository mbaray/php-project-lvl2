<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParser(string $type): callable
{
    $json = function (string $fileContent): array {
        return json_decode($fileContent, true);
    };

    $yaml = function (string $fileContent): array {
        return Yaml::parse($fileContent);
    };

    $default = function (string $fileContent): array {
        return [];
    };

    switch ($type) {
        case 'json':
        case 'JSON':
            return $json;

        case 'yml':
        case 'yaml':
        case 'YML':
        case 'YAML':
            return $yaml;

        default:
            return $default;
    }
}
