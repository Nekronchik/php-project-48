<?php

namespace Differ\Formatter\Plain;

function formatPlain(array $diffTree): string
{
    $plainOutput = buildPlainOutput($diffTree);
    return implode("\n", flattenArray($plainOutput));
}

function buildPlainOutput(array $tree, string $path = ''): array
{
    return array_map(function ($node) use ($path) {
        $propertyName = "{$path}{$node['name']}";
        $changeType = $node['type'];

        switch ($changeType) {
            case 'added':
                $value = formatValuePlain($node['value']);
                return "Property '{$propertyName}' was added with value: {$value}";
            case 'removed':
                return "Property '{$propertyName}' was removed";
            case 'changed':
                $oldValue = formatValuePlain($node['valueBefore']);
                $newValue = formatValuePlain($node['valueAfter']);
                return "Property '{$propertyName}' was updated. From {$oldValue} to {$newValue}";
            case 'nested':
                return buildPlainOutput($node['children'], "{$propertyName}.");
            default:
                return [];
        }
    }, $tree);
}

function formatValuePlain($value): string
{
    if (is_array($value) || is_object($value)) {
        return '[complex value]';
    }

    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        $value === null => 'null',
        default => "'$value'"
    };
}

function flattenArray(array $nestedArray): array
{
    return array_reduce($nestedArray, function ($flattened, $element) {
        return is_array($element) ? array_merge($flattened, flattenArray($element)) : array_merge($flattened, [$element]);
    }, []);
}