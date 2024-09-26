<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, string $dataFormat): object
{
    return match (strtolower($dataFormat)) {
        'json' => json_decode($data),
        'yml', 'yaml' => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception("Wrong file format '$dataFormat' or not supported")
    };
}