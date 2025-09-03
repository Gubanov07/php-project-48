<?php

namespace App\Differ;

use function App\Parsers\parseFile;

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = (array) parseFile($filePath1);
    $data2 = (array) parseFile($filePath2);
    
    $diff = buildDiff($data1, $data2);
    return stylish($diff);
}

function buildDiff(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortKeys($allKeys);
    
    $diff = [];
    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;
        
        if (!array_key_exists($key, $data2)) {
            $diff[] = ['type' => 'removed', 'key' => $key, 'value' => $value1];
            continue;
        }
        
        if (!array_key_exists($key, $data1)) {
            $diff[] = ['type' => 'added', 'key' => $key, 'value' => $value2];
            continue;
        }
        
        $value1 = is_object($value1) ? (array) $value1 : $value1;
        $value2 = is_object($value2) ? (array) $value2 : $value2;
        
        if (is_array($value1) && is_array($value2)) {
            $diff[] = [
                'type' => 'nested',
                'key' => $key,
                'children' => buildDiff($value1, $value2)
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

function stylish(array $diff, int $depth = 0): string
{
    $lines = [];
    $indent = str_repeat(' ', $depth * 4);
    
    foreach ($diff as $node) {
        switch ($node['type']) {
            case 'added':
                $lines[] = "{$indent}  + {$node['key']}: " . formatValue($node['value'], $depth + 1);
                break;
            case 'removed':
                $lines[] = "{$indent}  - {$node['key']}: " . formatValue($node['value'], $depth + 1);
                break;
            case 'unchanged':
                $lines[] = "{$indent}    {$node['key']}: " . formatValue($node['value'], $depth + 1);
                break;
            case 'changed':
                $lines[] = "{$indent}  - {$node['key']}: " . formatValue($node['oldValue'], $depth + 1);
                $lines[] = "{$indent}  + {$node['key']}: " . formatValue($node['newValue'], $depth + 1);
                break;
            case 'nested':
                $lines[] = "{$indent}    {$node['key']}: {";
                $lines[] = stylish($node['children'], $depth + 1);
                $lines[] = "{$indent}    }";
                break;
        }
    }
    
    return implode("\n", $lines);
}

function formatValue($value, int $depth = 0): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    if (is_null($value)) {
        return 'null';
    }
    
    if (is_object($value)) {
        $value = (array) $value;
    }
    
    if (is_array($value)) {
        $indent = str_repeat(' ', $depth * 4);
        $lines = ['{'];
        
        foreach ($value as $key => $val) {
            $lines[] = "{$indent}    {$key}: " . formatValue($val, $depth + 1);
        }
        $lines[] = "{$indent}}";
        return implode("\n", $lines);
    }
    
    return (string) $value;
}

function sortKeys(array $keys): array
{
    $sorted = $keys;
    sort($sorted);
    return $sorted;
}
