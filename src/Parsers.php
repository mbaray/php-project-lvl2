<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($fileСontent, string $type): array
{
    switch ($type) {
        case 'json':
            return json_decode($fileСontent, true);
            break;

        case 'yml':
        case 'yaml':
            return get_object_vars(Yaml::parse($fileСontent, Yaml::PARSE_OBJECT_FOR_MAP));
            break;

        default:
            return [];
            break;
    }
}
