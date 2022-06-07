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

function getKey(array $arr): string
{
    return $arr['key'];
}

// function getValue(array $arr)
// {
//     if () {

//     }
// }

function getOperation(array $arr): string
{
    return $arr['operation'];
}

function isDeleted(array $arr): bool
{
    return getOperation($arr) === 'deleted';
}

function isChanged(array $arr): bool
{
    return getOperation($arr) === 'changed';
}

function notChanged(array $arr): bool
{
    return !array_key_exists('operation', $arr) || getOperation($arr) === 'not_changed';
}

function makeType(mixed $value): string
{
    return is_array($value) ? 'object' : 'simple';
}
