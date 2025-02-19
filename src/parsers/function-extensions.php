<?php

namespace Loilo\JsonPath;

function apply_function($func, $root_node, $node)
{
	$length_function = length_function();
	$count_function = count_function();
	$match_function = match_function();
	$search_function = search_function();
	$value_function = value_function();

	$evaluated_args = array_map(function ($arg) use ($root_node, $node) {
		return apply_function_argument($arg, $root_node, $node);
	}, $func->args);

	switch ($func->name) {
		case 'length':
			$args = extract_args($length_function, $evaluated_args);
			return $length_function->function(...$args);
		case 'count':
			$args = extract_args($count_function, $evaluated_args);
			return $count_function->function(...$args);
		case 'match':
			$args = extract_args($match_function, $evaluated_args);
			return $match_function->function(...$args);
		case 'search':
			$args = extract_args($search_function, $evaluated_args);
			return $search_function->function(...$args);
		case 'value':
			$args = extract_args($value_function, $evaluated_args);
			return $value_function->function(...$args);
	}

	return nothing();
}

function apply_function_argument($argument, $root_node, $json)
{
	switch ($argument->type) {
		case 'Literal':
			return $argument->member;
		case 'CurrentNode':
			return apply_current_node($argument, $root_node, [$json]);
		case 'Root':
			return apply_root($argument, $root_node);
		case 'FunctionExpr':
			return apply_function($argument, $root_node, $json);
		default:
			throw new \Exception('Unknown argument type "' . $argument->type . '"');
	}
}
