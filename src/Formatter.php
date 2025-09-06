<?php

namespace App\Formatter;

use function App\Formatters\Stylish\format as formatStylish;
use function App\Formatters\Plain\format as formatPlain;
use function App\Formatters\Json\format as formatJson;

function format(array $diff, string $formatName): string
{
    switch ($formatName) {
        case 'stylish':
            return formatStylish($diff);
        case 'plain':
            return formatPlain($diff);
        case "json":
            return formatJson($diff);
        default:
            throw new \Exception("Unknown format: '{$formatName}'");
    }
}