<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

function readFile(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception("File '{$filePath}' does not exist");
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new \Exception("Failed to read file '{$filePath}'");
    }

    return $content;
}

function parseJson(string $content): object
{
    $data = json_decode($content);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Invalid JSON: " . json_last_error_msg());
    }

    return $data;
}

function parseYaml(string $content): object
{
    try {
        $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        return (object) $data;
    } catch (Exception $e) {
        throw new Exception("Invalid YAML: " . $e->getMessage());
    }
}

function parseFile(string $filePath): object
{
    $content = readFile($filePath);
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'json':
            return parseJson($content);
        case 'yml':
        case 'yaml':
            return parseYaml($content);
        default:
            throw new Exception("Unsupported file format: '{$extension}'");
    }
}
