<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Formatter\format as formatDiff;

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $data1 = (array) parseFile($filePath1);
    $data2 = (array) parseFile($filePath2);

    $diff = buildDiff($data1, $data2);
    return formatDiff($diff, $format);
}

function buildDiff(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = array_values(array_sort($allKeys));

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

        if (is_array($value1) && is_array($value2)) {
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

function array_sort(array $array): array 
{
    if (count($array) <= 1) {
        return $array;
    }
    
    $pivot = $array[0];
    $left = array_filter(array_slice($array, 1), fn($x) => $x < $pivot);
    $right = array_filter(array_slice($array, 1), fn($x) => $x >= $pivot);
    
    return array_merge(array_sort($left), [$pivot], array_sort($right));
}
