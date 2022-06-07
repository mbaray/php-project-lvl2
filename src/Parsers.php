<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;

function getParser(string $type): callable
{
    return match (strtolower($type)) {
        'yml', 'yaml' => fn(string $yaml) => Yaml::parse($yaml),
        'json' => fn(string $json) => json_decode($json, true),
        default => throw new InvalidArgumentException("Format {$type} is not supported"),
    };
}
