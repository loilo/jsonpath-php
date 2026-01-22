<?php

use Loilo\JsonPath\JsonPath;
use Loilo\JsonPath\PathResult;

function test_json_path($json, $jsonpath, $expected)
{
	$path = new JsonPath($jsonpath);
	expect($path->find($json))->toEqual($expected);
}

function test_json_path_ignoring_array_order($json, $jsonpath, $expected)
{
	$path = new JsonPath($jsonpath);
	$result = $path->find($json);
	expect($result)->toEqualCanonicalizing($expected);
}

function test_normalized_path($json, $jsonpath, $expected)
{
	$path = new JsonPath($jsonpath);
	$paths = array_map(function (PathResult $item) {
		return $item->path;
	}, $path->paths($json));

	expect($paths)->toEqual($expected);
}
