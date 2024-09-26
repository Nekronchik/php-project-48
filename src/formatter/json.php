<?php

namespace Differ\Formatter\Json;

function formatJson($tree): string
{
    return (string) json_encode($tree, JSON_PRETTY_PRINT);
}