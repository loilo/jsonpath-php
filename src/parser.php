<?php

namespace Loilo\JsonPath;

function run($json, $query): array
{
	$root_node = create_node($json, ['$']);
	return apply_root($query, $root_node);
}
