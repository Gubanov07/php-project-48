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
    $sortedKeys = array_sort($allKeys);

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
                'children' => buildDiff($value1, $value2)
            ]]);
        }

        if ($processedValue1 === $processedValue2) {
            return array_merge($acc, [['type' => 'unchanged', 'key' => $key, 'value' => $processedValue1]]);
        }

        return array_merge($acc, [['type' => 'changed', 'key' => $key, 'oldValue' => $value1, 'newValue' => $processedValue2]]);
    }, []);
}

function array_sort(array $array): array
{
return array_reduce($array, function ($acc, $item) {
        $index = array_reduce(array_keys($acc), function ($idx, $i) use ($acc, $item) {
            return $item < $acc[$i] ? $i : $idx;
        }, count($acc));
        
        return array_merge(
            array_slice($acc, 0, $index),
            [$item],
            array_slice($acc, $index)
        );
    }, []);
}
