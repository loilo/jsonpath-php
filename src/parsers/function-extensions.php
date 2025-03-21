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

	return match ($func->name) {
		'length' => $length_function->function(...extract_args($length_function, $evaluated_args)),
		'count' => $count_function->function(...extract_args($count_function, $evaluated_args)),
		'match' => $match_function->function(...extract_args($match_function, $evaluated_args)),
		'search' => $search_function->function(...extract_args($search_function, $evaluated_args)),
		'value' => $value_function->function(...extract_args($value_function, $evaluated_args)),
		default => nothing(),
	};
}

function apply_function_argument($argument, $root_node, Node $node)
{
	return match ($argument->type) {
		'Literal' => $argument->member,
		'CurrentNode' => apply_current_node($argument, $root_node, [$node]),
		'Root' => apply_root($argument, $root_node),
		'FunctionExpr' => apply_function($argument, $root_node, $node),
		default => throw new \Exception('Unknown argument type "' . $argument->type . '"'),
	};
}
