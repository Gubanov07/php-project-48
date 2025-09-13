<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

function parseFile(string $extension, string $content): array
{
    switch ($extension) {
        case 'json':
            return json_decode($content, true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($content);
        default:
            throw new Exception("Unsupported file format: '{$extension}'");
    }
}
