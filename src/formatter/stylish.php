<?php

namespace Differ\Formatter\Stylish;

function formatStylish(array $tree): string
{
    return formatTree($tree);
}

function formatTree(array $tree, int $depth = 0): string
{
    $indent = str_repeat("    ", $depth);
    $result = array_map(function ($item) use ($depth, $indent) {
        $name = $item['name'];
        $type = $item['type'];
        $valueIndent = str_repeat("    ", $depth + 1);

        switch ($type) {
            case 'added':
                return "{$valueIndent}+ {$name}: " . formatValue($item['value'], $depth + 1);
            case 'removed':
                return "{$valueIndent}- {$name}: " . formatValue($item['value'], $depth + 1);
            case 'unchanged':
                return "{$valueIndent}  {$name}: " . formatValue($item['value'], $depth + 1);
            case 'changed':
                $before = formatValue($item['valueBefore'], $depth + 1);
                $after = formatValue($item['valueAfter'], $depth + 1);
                return "{$valueIndent}- {$name}: {$before}\n{$valueIndent}+ {$name}: {$after}";
            case 'nested':
                $children = formatTree($item['children'], $depth + 1);
                return "{$valueIndent}  {$name}: {\n{$children}\n{$valueIndent}}";
        }
    }, $tree);

    return implode("\n", $result);
}

function formatValue($value, int $depth): string
{
    if (is_array($value) || is_object($value)) {
        $indent = str_repeat("    ", $depth + 1);
        $values = array_map(fn($key, $val) => "{$indent}{$key}: " . formatValue($val, $depth + 1), array_keys((array) $value), (array) $value);
        return "{\n" . implode("\n", $values) . "\n" . str_repeat("    ", $depth) . "}";
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

    return (string)$value;
}