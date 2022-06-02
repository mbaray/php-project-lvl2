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
            return Yaml::parse($fileContent);

        default:
            return [];
    }
}
