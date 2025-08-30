<?php

namespace App\Parsers;

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

function parseFile(string $filePath): array
{
    $content = readFile($filePath);
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    switch ($extension) {
        case 'json':
            return (array) parseJson($content);
        default:
            throw new \Exception("Unsupported file format: '{$extension}'");
    }
}
