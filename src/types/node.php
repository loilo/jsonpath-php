<?php

namespace Loilo\JsonPath;

use Loilo\JsonPath\Arr;

class Node
{
	public function __construct(public mixed $value, public array $path) {}
}

function create_node(mixed $json, array $path): Node
{
	return new Node($json, $path);
}

function add_member_path(Node $base, mixed $new_value, string $member_name): Node
{
	$escaped_member_name = escape_member_name($member_name);

	return create_node($new_value, [...$base->path, $escaped_member_name]);
}

function add_index_path(Node $base, mixed $new_value, int $index): Node
{
	return create_node($new_value, [...$base->path, $index]);
}

function is_node($node)
{
	return $node instanceof Node;
}

function is_node_list($obj)
{
	if (!Arr\is_list($obj)) {
		return false;
	}
	return Arr\every($obj, __NAMESPACE__ . '\is_node');
}
