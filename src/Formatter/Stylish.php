<?php

namespace Differ\Formatters\Stylish;

function format(array $diff, int $depth = 0): string
{
    $lines = [];
    $indent = str_repeat('    ', $depth);
    
    foreach ($diff as $node) {
        $key = $node['key'];
        
        switch ($node['type']) {
            case 'added':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$indent}+ {$key}: {$value}";
                break;
            case 'removed':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$indent}- {$key}: {$value}";
                break;
            case 'unchanged':
                $value = stringify($node['value'], $depth);
                $lines[] = "{$indent}    {$key}: {$value}";
                break;
            case 'changed':
                $oldValue = stringify($node['oldValue'], $depth);
                $newValue = stringify($node['newValue'], $depth);
                $lines[] = "{$indent}- {$key}: {$oldValue}";
                $lines[] = "{$indent}+ {$key}: {$newValue}";
                break;
            case 'nested':
                $children = format($node['children'], $depth + 1);
                $lines[] = "{$indent}    {$key}: {";
                $lines[] = $children;
                $lines[] = "{$indent}    }";
                break;
        }
    }
    
    return implode("\n", $lines);
}

function stringify($value, int $depth): string
{
    if (is_array($value) && isset($value['key5'])) {
        echo "DEPTH: $depth\n";
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    if (is_null($value)) {
        return 'null';
    }
    
    if (is_array($value) || is_object($value)) {
        $value = (array) $value;
        $indent = str_repeat('    ', $depth);
        $innerIndent = str_repeat('    ', $depth + 1);
        $lines = [];
        
        foreach ($value as $key => $val) {
            $lines[] = "{$innerIndent}{$key}: " . stringify($val, $depth + 1);
        }
        
        return "{\n" . implode("\n", $lines) . "\n{$indent}}";
    }
    
    return (string) $value;
}
