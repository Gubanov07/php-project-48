<?php

namespace App\Formatters\Stylish;

function format(array $diff, int $depth = 0): string
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
                $lines[] = format($node['children'], $depth + 1);
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