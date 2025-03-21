<?php

namespace Loilo\JsonPath;

/**
 * @return Node[]
 */
function traverse_descendant(Node $node)
{
	$node_list = [];
	array_push($node_list, $node);

	foreach (enumerate_node($node) as $child) {
		array_push($node_list, ...traverse_descendant($child));
	}

	return $node_list;
}
