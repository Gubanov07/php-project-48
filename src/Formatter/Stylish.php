<?php

namespace Differ\Formatters\Stylish;

function format(array $diff, int $depth = 0): string
{
    $lines = [];
    $baseIndent = str_repeat('    ', $depth);
    $signIndent = str_repeat('      ', $depth);
    
    foreach ($diff as $node) {
        $key = $node['key'];
        
        switch ($node['type']) {
            case 'added':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$signIndent}+ {$key}: {$value}";
                break;
            case 'removed':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$signIndent}- {$key}: {$value}";
                break;
            case 'unchanged':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$baseIndent}    {$key}: {$value}";
                break;
            case 'changed':
                $oldValue = stringify($node['oldValue'], $depth);
                $newValue = stringify($node['newValue'], $depth);
                $lines[] = "{$signIndent}- {$key}: {$oldValue}";
                $lines[] = "{$signIndent}+ {$key}: {$newValue}";
                break;
            case 'nested':
                $children = format($node['children'], $depth + 1);
                $lines[] = "{$baseIndent}    {$key}: {";
                $lines[] = $children;
                $lines[] = "{$baseIndent}    }";
                break;
        }
    }
    
    $result = implode("\n", $lines);
    
    if ($depth === 0) {
        $result = "{\n" . $result . "\n}";
    }
    
    return $result;
}
function stringify($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    if (is_null($value)) {
        return 'null';
    }
    
    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }
    
    return (string) $value;
}
