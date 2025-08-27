<?php

namespace App\Differ;

use function App\Parsers\parseFile;
use function Funct\Collection\sortBy;

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = (array) parseFile($filePath1);
    $data2 = (array) parseFile($filePath2);
    
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortBy($allKeys, fn($key) => $key);
    
    $lines = [];
    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;
        
        if (!array_key_exists($key, $data2)) {
            $lines[] = "  - {$key}: " . formatValue($value1);
            continue;
        }
        
        if (!array_key_exists($key, $data1)) {
            $lines[] = "  + {$key}: " . formatValue($value2);
            continue;
        }
        
        if ($value1 === $value2) {
            $lines[] = "    {$key}: " . formatValue($value1);
            continue;
        }
        
        $lines[] = "  - {$key}: " . formatValue($value1);
        $lines[] = "  + {$key}: " . formatValue($value2);
    }
    
    return "{\n" . implode("\n", $lines) . "\n}";
}

function formatValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    if (is_null($value)) {
        return 'null';
    }
    
    return (string) $value;
}