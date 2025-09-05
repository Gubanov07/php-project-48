<?php

namespace App\Formatter;

use function App\Formatters\Stylish\format as formatStylish;
use function App\Formatters\Plain\format as formatPlain;

function format(array $diff, string $formatName): string
{
    switch ($formatName) {
        case 'stylish':
            return formatStylish($diff);
        case 'plain':
            return formatPlain($diff);
        default:
            throw new \Exception("Unknown format: '{$formatName}'");
    }
}