<?php

namespace Loilo\JsonPath;

function traverse_descendant($json)
{
	$nodelist = [];
	array_push($nodelist, $json);

	if (is_json_array($json)) {
		foreach ($json as $node) {
			array_push($nodelist, ...traverse_descendant($node));
		}
	} elseif (is_json_object($json)) {
		foreach ((array) $json as $value) {
			array_push($nodelist, ...traverse_descendant($value));
		}
	}

	return $nodelist;
}
