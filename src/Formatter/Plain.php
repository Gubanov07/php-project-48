<?php

namespace Differ\Formatters\Plain;

function format(array $diff): string
{
    $lines = buildPlainLines($diff);
    return implode("\n", $lines);
}

function buildPlainLines(array $diff, string $path = ''): array
{
    return array_reduce($diff, function ($acc, $node) use ($path) {
        $currentPath = $path === '' ? $node['key'] : "{$path}.{$node['key']}";

        switch ($node['type']) {
            case 'added':
                return array_merge($acc, ["Property '{$currentPath}' was added with value: " . toString($node['value'])]);
            case 'removed':
                return array_merge($acc, ["Property '{$currentPath}' was removed"]);
            case 'changed':
                return array_merge($acc, ["Property '{$currentPath}' was updated. From " . toString($node['oldValue']) . " to " . toString($node['newValue'])]);
            case 'nested':
                return array_merge($acc, buildPlainLines($node['children'], $currentPath));
            default:
                return $acc;
        }
    }, []);
}

function toString(mixed $value): string
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
