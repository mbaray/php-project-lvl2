<?php

namespace Differ\Formatters\Json;

function format(array $ast): string
{
    return json_encode($ast);
}
