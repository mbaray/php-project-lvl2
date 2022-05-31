<?php

namespace Differ\String;

function toString(mixed $value): string
{
    $exportString = var_export($value, true);

    return is_string($value) ? $value : trim(mb_strtolower($exportString), "'");
}

function toStringTxt(mixed $value): string
{
    $string = toString($value);

    return is_string($value) ? "'{$string}'" : $string;
}
