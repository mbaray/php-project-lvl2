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
            return $json;

        case 'yml':
        case 'yaml':
            return $yaml;

        default:
            return $default;
    }
}
