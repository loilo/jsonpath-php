<?php

namespace Loilo\JsonPath;

// 2.2. Root Identifier
// Every JSONPath query MUST begin with the root identifier $.
function apply_root($root, $root_node)
{
	return apply_segments($root->segments, $root_node, [$root_node]);
}

// 2.5. Segments
// For each node in an input nodelist, segments apply one or more selectors
// to the node and concatenate the results of each selector into per - input - node nodelists,
// which are then concatenated in the order of the input nodelist to form a single segment result nodelist.
function apply_segments(array $segments, $root_node, $node_list)
{
	$result = array_reduce(
		$segments,
		function ($result_node_list, $current_segment) use ($root_node) {
			return Arr\flat_map(
				fn($node) => apply_segment($current_segment, $root_node, $node),
				$result_node_list,
			);
		},
		$node_list,
	);
	return $result;
}

// For each node in the input nodelist,
// the resulting nodelist of a child segment is the concatenation of the nodelists
// from each of its selectors in the order that the selectors appear in the list.
// Note: Any node matched by more than one selector is kept as many times in the nodelist.
function apply_segment($segment, $root_node, Node $node)
{
	if (is_json_array($segment)) {
		// ChildSegment
		$selector_results = array_map(function ($selector) use ($root_node, $node) {
			return apply_selector($selector, $root_node, $node);
		}, $segment);
		$segment_result = Arr\flatten($selector_results);

		return $segment_result;
	}

	// DescendantSegment
	$descendant_nodes = traverse_descendant($node);

	return Arr\flat_map(
		fn($node) => Arr\flat_map(
			fn($selector) => apply_selector($selector, $root_node, $node),
			$segment->selectors,
		),
		$descendant_nodes,
	);
}

// 2.3. Selectors
// A selector produces a nodelist consisting of zero or more children of the input value.
function apply_selector($selector, $root_node, Node $node)
{
	return match ($selector->type) {
		'WildcardSelector' => apply_wildcard_selector($selector, $node),
		'IndexSelector' => apply_index_selector($selector, $node),
		'SliceSelector' => apply_slice_selector($selector, $node),
		'MemberNameShorthand', 'NameSelector' => apply_member_name_selector($selector, $node),
		'FilterSelector' => apply_filter_selector($selector, $root_node, $node),
		default => throw new \Exception('Unknown selector type: ' . $selector->type),
	};
}

// 2.3.2. Wildcard Selector
// A wildcard selector selects the nodes of all children of an object or array.
function apply_wildcard_selector($selector, Node $node)
{
	$results = [];
	$json = $node->value;

	if (is_json_array($json)) {
		foreach ($json as $key => $item) {
			$results[] = add_index_path($node, $item, intval($key));
		}
	} elseif (is_json_object($json)) {
		foreach ((array) $json as $key => $item) {
			$results[] = add_member_path($node, $item, $key);
		}
	}

	return $results;
}

// 2.3.1. Name Selector
// A name selector '<name>' selects at most one object member value.
function apply_member_name_selector($selector, Node $node)
{
	// Nothing is selected from a value that is not an object.
	if (!is_json_object($node->value)) {
		return [];
	}

	// Applying the name-selector to an object node
	// selects a member value whose name equals the member name M
	if (json_object_has_key($node->value, $selector->member)) {
		return [
			add_member_path($node, json_get($node->value, $selector->member), $selector->member),
		];
	}

	return [];
}

// 2.3.3. Index Selector
// An index selector <index> matches at most one array element value.
function apply_index_selector($selector, Node $node)
{
	if (!is_json_array($node->value)) {
		return [];
	}

	$index = Arr\normalize_index($selector->index, sizeof($node->value));

	return $index === null ? [] : [add_index_path($node, $node->value[$index], $index)];
}
