<?php

namespace Loilo\JsonPath;

function traverse_descendant($json)
{
	$node_list = [];
	array_push($node_list, $json);

	if (is_json_array($json)) {
		foreach ($json as $node) {
			array_push($node_list, ...traverse_descendant($node));
		}
	} elseif (is_json_object($json)) {
		foreach ((array) $json as $value) {
			array_push($node_list, ...traverse_descendant($value));
		}
	}

	return $node_list;
}
