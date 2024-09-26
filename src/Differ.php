<?php

namespace Differ\Differ;

use Docopt;

use function Differ\Parsers\parse;
use function Differ\Formatter\Stylish\formatStylish;
use function Differ\Formatter\Plain\formatPlain;
use function Differ\Formatter\Json\formatJson;

function genDiff($filePathBefore, $filePathAfter, $outputFormat = 'stylish')
{
    $beforeContent = file_get_contents($filePathBefore);
    $afterContent = file_get_contents($filePathAfter);

    $beforeFileType = pathinfo($filePathBefore, PATHINFO_EXTENSION);
    $afterFileType = pathinfo($filePathAfter, PATHINFO_EXTENSION);

    $parsedBeforeData = parse($beforeContent, $beforeFileType);
    $parsedAfterData = parse($afterContent, $afterFileType);

    $diffTree = buildDiffTree($parsedBeforeData, $parsedAfterData);

    switch ($outputFormat) {
        case 'plain':
            return formatPlain($diffTree);
        case 'json':
            return formatJson($diffTree);
        default:
            return formatStylish($diffTree);
    }
}

function buildDiffTree(object $beforeData, object $afterData): array
{
    $uniqueKeys = union(array_keys(get_object_vars($beforeData)), array_keys(get_object_vars($afterData)));

    $sortedKeys = array_values(
        sortBy(
            $uniqueKeys,
            function ($key) {
                return $key;
            }
        )
    );

    $diffTree = array_map(
        function ($key) use ($beforeData, $afterData) {
            if (!property_exists($afterData, $key)) {
                return [
                    'name' => $key,
                    'type' => 'removed',
                    'value' => $beforeData->$key
                ];
            }
            if (!property_exists($beforeData, $key)) {
                return [
                    'name' => $key,
                    'type' => 'added',
                    'value' => $afterData->$key
                ];
            }
            if (is_object($beforeData->$key) && is_object($afterData->$key)) {
                return [
                    'name' => $key,
                    'type' => 'nested',
                    'children' => buildDiffTree($beforeData->$key, $afterData->$key)
                ];
            }
            if ($beforeData->$key !== $afterData->$key) {
                return [
                    'name' => $key,
                    'type' => 'changed',
                    'oldValue' => $beforeData->$key,
                    'newValue' => $afterData->$key
                ];
            }
            return [
                'name' => $key,
                'type' => 'unchanged',
                'value' => $beforeData->$key
            ];
        },
        $sortedKeys
    );
    return $diffTree;
}

function sortBy($collection, $comparator, $sortFunction = 'asort')
{
    if (!is_callable($comparator)) {
        $comparator = function ($item) use ($comparator) {
            return $item[$comparator];
        };
    }

    $values = array_map($comparator, $collection);
    $sortFunction($values);

    $sortedCollection = [];
    foreach ($values as $key => $value) {
        $sortedCollection[$key] = $collection[$key];
    }

    return $sortedCollection;
}

function union($firstCollection, $secondCollection)
{
    $merged = array_merge($firstCollection, $secondCollection);

    return array_unique($merged);
}