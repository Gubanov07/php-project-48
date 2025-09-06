<?php

namespace App\Formatters\Json;

function format(array $diff): string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
