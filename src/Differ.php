<?php

namespace Differ\Differ;

use function cli\line;
function parser($pathToBefore, $pathToAfter)
{
    $rawBefore = file_get_contents($pathToBefore);
    $rawAfter = file_get_contents($pathToAfter);
    $before = json_decode($rawBefore, true);
    $after = json_decode($rawAfter, true);

    return [$before, $after];
}

function getAdded($before, $after)
{
    $added = array_diff_assoc($after, $before);
    return $added;
}
function getRemoved($before, $after)
{
    $removed = array_diff_assoc($before, $after);
    return $removed;
}

function getStill($before, $after)
{
    $still = array_intersect_assoc($before, $after);
    return $still;
}
function getKeys($before, $after)
{
    $beforeKeys = array_keys($before);
    $afterKeys = array_keys($after);
    $keys = array_merge(
        array_intersect($beforeKeys, $afterKeys),
        array_diff($beforeKeys, $afterKeys),
        array_diff($afterKeys, $beforeKeys)
    );
    sort($keys);
    return array_values($keys);
}

function genDiff($pathToBefore, $pathToAfter)
{
    [$before, $after] = parser($pathToBefore, $pathToAfter);

    $added = getAdded($before, $after);
    $removed = getRemoved($before, $after);
    $still = getStill($before, $after);
    $keys = getKeys($before, $after);

    print("{\n");
    foreach ($keys as $key) {
        if (isset($removed[$key])) {
            print("    - {$key}: {$removed[$key]}\n");
        }
        if (isset($added[$key])) {
            print("    + {$key}: {$added[$key]}\n");
        }
        if (isset($still[$key])) {
            print("      {$key}: {$still[$key]}\n");
        }
    }
    print("}\n");
}