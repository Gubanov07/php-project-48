<?php

namespace Differ\Formatters\Json;

function format(array $diff): string
{
    $result = json_encode($diff, JSON_PRETTY_PRINT);
    return $result === false ? '' : $result;
}
