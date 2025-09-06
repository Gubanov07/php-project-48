<?php

namespace App\Formatters\Plain;

function format(array $diff): string
{
    $lines = [];
    buildPlainLines($diff, $lines);
    return implode("", $lines);
}

function buildPlainLines(array $diff, array &$lines, string $path = ''): void
{
    foreach ($diff as $node) {
        $currentPath = $path ? "{$path}.{$node['key']}" : $node['key'];
        
        switch ($node['type']) {
            case 'added':
                $lines[] = "Property '{$currentPath}' was added with value: " . toString($node['value']);
                break;
            case 'removed':
                $lines[] = "Property '{$currentPath}' was removed";
                break;
            case 'changed':
                $lines[] = "Property '{$currentPath}' was updated. From " . toString($node['oldValue']) . " to " . toString($node['newValue']);
                break;
            case 'nested':
                buildPlainLines($node['children'], $lines, $currentPath);
                break;
        }
    }
}

function toString($value): string
{
    if (is_object($value) || is_array($value)) {
        return '[complex value]';
    }
    
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    if (is_null($value)) {
        return 'null';
    }
    
    if (is_string($value)) {
        return "'{$value}'";
    }
    
    return (string) $value;
}