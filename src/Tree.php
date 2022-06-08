<?php

namespace Differ\Tree;

function makeNode(string $key, mixed $value, string $operation = ''): array
{
    switch ($operation) {
        case 'changed':
            [$oldValue, $newValue] = $value;

            return [
                'operation' => $operation,
                'oldType' => makeType($oldValue),
                'newType' => makeType($newValue),
                'key' => $key,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
            ];

        case 'added':
        case 'deleted':
        case 'not_changed':
            return [
                'operation' => $operation,
                'type' => makeType($value),
                'key' => $key,
                'value' => $value,
            ];

        default:
            return [
                'type' => makeType($value),
                'key' => $key,
                'value' => $value,
            ];
    }
}

function getKey(array $node): string
{
    return $node['key'];
}

function getValue(array $node): mixed
{
    return $node['value'];
}

function getOldValue(array $node): mixed
{
    return $node['oldValue'];
}

function getNewValue(array $node): mixed
{
    return $node['newValue'];
}

function getOperation(array $node): string
{
    return $node['operation'];
}

function isAdded(array $node): bool
{
    return getOperation($node) === 'added';
}

function isDeleted(array $node): bool
{
    return getOperation($node) === 'deleted';
}

function isChanged(array $node): bool
{
    return getOperation($node) === 'changed';
}

function notChanged(array $node): bool
{
    return getOperation($node) === 'not_changed';
}

function isObject(mixed $value): bool
{
    return is_array($value);
}

function makeType(mixed $value): string
{
    return isObject($value) ? 'object' : 'simple';
}
