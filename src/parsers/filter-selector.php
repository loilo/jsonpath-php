<?php

namespace Loilo\JsonPath;

function apply_filter_selector($selector, $root_node, $json)
{
	// The filter selector works with arrays and objects exclusively.
	// Its result is a list of(zero, one, multiple, or all)
	// their array elements or member values, respectively.
	// Applied to a primitive value,
	// it selects nothing(and therefore does not contribute to the result of the filter selector).
	if (is_json_primitive($json)) {
		return [];
	}

	return Array\filter(to_array($json), function ($item) use ($selector, $root_node) {
		return apply_filter_expression($selector->expr, $root_node, $item);
	});
}

function apply_filter_expression($expr, $root_node, $json)
{
	$exp_type = $expr->type;
	return match ($exp_type) {
		'ComparisonExpr' => apply_compare($expr, $root_node, $json),
		'TestExpr' => apply_test($expr, $root_node, $json),
		'LogicalBinary', 'LogicalUnary' => apply_logical($expr, $root_node, $json),
		default => throw new \Exception("Unexpected expression type: $exp_type"),
	};
}

function apply_compare($compare, $root_node, $json)
{
	$left = apply_comparable($compare->left, $root_node, $json);
	$right = apply_comparable($compare->right, $root_node, $json);

	return eval_compare($left, $right, $compare->operator);
}

function eval_compare($left, $right, $operator)
{
	if ($left === nothing() || $right === nothing()) {
		return compare_nodes($operator, $left, $right);
	}

	$left_value = $left;
	$right_value = $right;

	if (is_logical_type($left_value) || is_logical_type($right_value)) {
		throw new \Exception("LogicalType can't be compared");
	}

	if (is_json_object($left_value) && is_json_object($right_value)) {
		return compare_objects($operator, $left_value, $right_value);
	}

	if (is_json_array($left_value) && is_json_array($right_value)) {
		return compare_arrays($operator, $left_value, $right_value);
	}

	if (is_numeric($left_value) && is_numeric($right_value)) {
		return compare_numbers($operator, $left_value, $right_value);
	}

	if (is_string($left_value) && is_string($right_value)) {
		return compare_strings($operator, $left_value, $right_value);
	}

	if (is_bool($left_value) && is_bool($right_value)) {
		return compare_booleans($operator, $left_value, $right_value);
	}

	if ($left_value === null && $right_value === null) {
		return compare_nulls($operator, $left_value, $right_value);
	}

	if ($operator === '!=') {
		return true;
	}

	return false;
}

function apply_current_node($current_node, $root_node, $node_list)
{
	return apply_segments($current_node->segments, $root_node, $node_list);
}

function apply_comparable($comparable, $root_node, $json)
{
	// These can be obtained via literal values; singular queries,
	// each of which selects at most one node
	return match ($comparable->type) {
		'Literal' => $comparable->member,
		'CurrentNode' => array_key_exists(
			0,
			$result = apply_current_node($comparable, $root_node, [$json]),
		)
			? $result[0]
			: nothing(),
		'Root' => apply_root($comparable, $root_node)[0] ?? nothing(),
		'FunctionExpr' => apply_function($comparable, $root_node, $json),
	};
}

function apply_test($expr, $root_node, $json)
{
	return apply_query($expr->query, $root_node, $json);
}

function apply_query($query, $root_node, $json)
{
	switch ($query->type) {
		case 'FunctionExpr':
			$function_result = apply_function($query, $root_node, $json);

			// LogicalType
			if ($function_result === LogicalType::true()) {
				return true;
			}
			if ($function_result === LogicalType::false()) {
				return false;
			}

			// NodesType
			if (is_json_array($function_result)) {
				return sizeof($function_result) > 0;
			}
			// ValueType
			throw new \Exception("Function {$query->name} result must be compared");
		case 'CurrentNode':
			return sizeof(apply_current_node($query, $root_node, [$json])) > 0;
		case 'Root':
			return sizeof(apply_root($query, $root_node)) > 0;
	}

	return false;
}

function apply_logical($expr, $root_node, $json)
{
	return match ($expr->operator) {
		'||' => apply_or($expr, $root_node, $json),
		'&&' => apply_and($expr, $root_node, $json),
		'!' => apply_not($expr, $root_node, $json),
	};
}

function apply_or($or, $root_node, $json)
{
	// TODO: make efficient
	$left = apply_filter_expression($or->left, $root_node, $json);
	$right = apply_filter_expression($or->right, $root_node, $json);
	return $left || $right;
}

function apply_and($and, $root_node, $json)
{
	$left = apply_filter_expression($and->left, $root_node, $json);
	$right = apply_filter_expression($and->right, $root_node, $json);
	return $left && $right;
}

function apply_not($not, $root_node, $json)
{
	$result = apply_filter_expression($not->expr, $root_node, $json);
	return !$result;
}
