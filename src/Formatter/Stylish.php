<?php

namespace Differ\Formatters\Stylish;

function format(array $diff, int $depth = 0): string
{
    $lines = [];
    $baseIndent = str_repeat('    ', $depth);
    $signIndent = $baseIndent . '  ';

    $lines = array_reduce($diff, function ($acc, $node) use ($depth, $baseIndent, $signIndent) {
        $key = $node['key'];

        switch ($node['type']) {
            case 'added':
                $value = stringify($node['value'], $depth + 1);
                return array_merge($acc, ["{$signIndent}+ {$key}: {$value}"]);
            case 'removed':
                $value = stringify($node['value'], $depth + 1);
                return array_merge($acc, ["{$signIndent}- {$key}: {$value}"]);
            case 'unchanged':
                $value = stringify($node['value'], $depth + 1);
                return array_merge($acc, ["{$baseIndent}    {$key}: {$value}"]);
            case 'changed':
                $oldValue = stringify($node['oldValue'], $depth + 1);
                $newValue = stringify($node['newValue'], $depth + 1);
                return array_merge($acc, [
                    "{$signIndent}- {$key}: {$oldValue}",
                    "{$signIndent}+ {$key}: {$newValue}"
                ]);
            case 'nested':
                $children = format($node['children'], $depth + 1);
                return array_merge($acc, [
                    "{$baseIndent}    {$key}: {",
                    $children,
                    "{$baseIndent}    }"
                ]);
            default:
                return $acc;
        }
    }, []);

    $result = implode("\n", $lines);

    if ($depth === 0) {
        $result = "{\n" . $result . "\n}";
    }

    return $result;
}

function stringify(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value) || is_object($value)) {
        $value = (array) $value;
        $baseIndent = str_repeat('    ', $depth);
        $innerIndent = str_repeat('    ', $depth + 1);

        $lines = array_reduce(array_keys($value), function ($acc, $key) use ($value, $depth, $innerIndent) {
            $val = stringify($value[$key], $depth + 1);
            return array_merge($acc, ["{$innerIndent}{$key}: {$val}"]);
        }, []);

        if (count($lines) === 0) {
            return "{}";
        }

        return "{\n" . implode("\n", $lines) . "\n{$baseIndent}}";
    }

    return (string) $value;
}
