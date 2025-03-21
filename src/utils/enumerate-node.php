<?php

namespace Loilo\JsonPath;

function enumerate_node(Node $node): array
{
	$json = $node->value;

	if (is_json_primitive($json)) {
		return [];
	}

	if (is_json_array($json)) {
		return array_map(
			fn($item, $index) => add_index_path($node, $item, $index),
			$json,
			array_keys($json),
		);
	}

	if (is_json_object($json)) {
		$array = (array) $json;
		return array_map(
			fn($value, $key) => add_member_path($node, $value, $key),
			array_values($array),
			array_keys($array),
		);
	}

	return [];
}
