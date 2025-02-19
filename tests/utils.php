<?php

use Loilo\JsonPath\JsonPath;

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
