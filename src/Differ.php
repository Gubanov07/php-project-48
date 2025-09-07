<?php

namespace App\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Formatter\format as formatDiff;

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $data1 = (array) parseFile($filePath1);
    $data2 = (array) parseFile($filePath2);
    
    $forPlain = ($format === 'plain');
    $diff = buildDiff($data1, $data2, $forPlain);
    return formatDiff($diff, $format);
}

function buildDiff(array $data1, array $data2, bool $forPlain = false): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortKeys($allKeys);
    
    $diff = [];
    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;
        
        if (!array_key_exists($key, $data2)) {
            if (!$forPlain && (is_object($value1) || is_array($value1))) {
                $value1 = (array) $value1;
                $diff[] = [
                    'type' => 'nested',
                    'key' => $key,
                    'children' => buildDiff($value1, [], $forPlain)
                ];
            } else {
                $diff[] = ['type' => 'removed', 'key' => $key, 'value' => $value1];
            }
            continue;
        }
        
        if (!array_key_exists($key, $data1)) {
            if (!$forPlain && (is_object($value2) || is_array($value2))) {
                $value2 = (array) $value2;
                $diff[] = [
                    'type' => 'nested',
                    'key' => $key,
                    'children' => buildDiff([], $value2, $forPlain)
                ];
            } else {
                $diff[] = ['type' => 'added', 'key' => $key, 'value' => $value2];
            }
            continue;
        }
        
        $value1 = is_object($value1) ? (array) $value1 : $value1;
        $value2 = is_object($value2) ? (array) $value2 : $value2;
        
        if (is_array($value1) && is_array($value2)) {
            $diff[] = [
                'type' => 'nested',
                'key' => $key,
                'children' => buildDiff($value1, $value2, $forPlain)
            ];
            continue;
        }
        
        if ($value1 === $value2) {
            $diff[] = ['type' => 'unchanged', 'key' => $key, 'value' => $value1];
            continue;
        }
        
        $diff[] = ['type' => 'changed', 'key' => $key, 'oldValue' => $value1, 'newValue' => $value2];
    }
    
    return $diff;
}

function sortKeys(array $keys): array
{
    $sorted = $keys;
    sort($sorted);
    return $sorted;
}