<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parsers\parseFile;
use function Differ\Formatter\getDesiredFormat as formatDiff;

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $data1 = makeParseFile($filePath1);
    $data2 = makeParseFile($filePath2);

    $diff = buildDiff($data1, $data2);
    return formatDiff($diff, $format);
}


function makeParseFile(string $filePath): array
{
    return parseFile(getExtension($filePath), getFileContent($filePath));
}

function getExtension(string $filePath): string
{
    return pathinfo(getAbsolutePathToFile($filePath), PATHINFO_EXTENSION);
}

function getAbsolutePathToFile(string $filePath): string
{
    $absolutePath = realpath($filePath);
    if ($absolutePath === false) {
        throw new Exception("File does not exist: $filePath");
    }
    return $absolutePath;
}

function getFileContent(string $filePath): string
{
    $content = file_get_contents(getAbsolutePathToFile($filePath));

    if ($content === false) {
        throw new Exception("File read error");
    }
    return $content;
}

function buildDiff(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortKeys($allKeys);

    return array_reduce($sortedKeys, function ($acc, $key) use ($data1, $data2) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (!array_key_exists($key, $data2)) {
            return array_merge($acc, [['type' => 'removed', 'key' => $key, 'value' => $value1]]);
        }

        if (!array_key_exists($key, $data1)) {
            return array_merge($acc, [['type' => 'added', 'key' => $key, 'value' => $value2]]);
        }

        $processedValue1 = is_object($value1) ? (array) $value1 : $value1;
        $processedValue2 = is_object($value2) ? (array) $value2 : $value2;

        if (is_array($processedValue1) && is_array($processedValue2)) {
            return array_merge($acc, [[
                'type' => 'nested',
                'key' => $key,
                'children' => buildDiff($processedValue1, $processedValue2)
            ]]);
        }

        if ($processedValue1 === $processedValue2) {
            return array_merge($acc, [['type' => 'unchanged', 'key' => $key, 'value' => $processedValue1]]);
        }

        return array_merge($acc, [['type' => 'changed', 'key' => $key, 'oldValue' => $processedValue1, 'newValue' => $processedValue2]]);
    }, []);
}

function sortKeys(array $keys): array
{
    return sort($keys, function ($a, $b) {
        return strcmp((string)$a, (string)$b);
    });
}
