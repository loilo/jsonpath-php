<?php

namespace Loilo\JsonPath;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;

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
			return Array\flat_map(
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
function apply_segment($segment, $root_node, $json)
{
	if (is_json_array($segment)) {
		// ChildSegment
		$selector_results = array_map(function ($selector) use ($root_node, $json) {
			return apply_selector($selector, $root_node, $json);
		}, $segment);
		$segment_result = Array\flatten($selector_results);

		return $segment_result;
	}

	// DescendantSegment
	$descendant_nodes = traverse_descendant($json);

	return Array\flat_map(
		fn($node) => Array\flat_map(
			fn($selector) => apply_selector($selector, $root_node, $node),
			$segment->selectors,
		),
		$descendant_nodes,
	);
}

// 2.3. Selectors
// A selector produces a nodelist consisting of zero or more children of the input value.
function apply_selector($selector, $root_node, $json)
{
	return match ($selector->type) {
		'WildcardSelector' => apply_wildcard_selector($selector, $json),
		'IndexSelector' => apply_index_selector($selector, $json),
		'SliceSelector' => apply_slice_selector($selector, $json),
		'MemberNameShorthand', 'NameSelector' => apply_member_name_selector($selector, $json),
		'FilterSelector' => apply_filter_selector($selector, $root_node, $json),
		default => throw new \Exception('Unknown selector type: ' . $selector->type),
	};
}

// 2.3.2. Wildcard Selector
// A wildcard selector selects the nodes of all children of an object or array.
function apply_wildcard_selector($node, $json)
{
	$results = [];

	if (is_json_array($json)) {
		foreach ($json as $a) {
			$results[] = $a;
		}
	} elseif (is_json_object($json)) {
		foreach ((array) $json as $a) {
			$results[] = $a;
		}
	}

	return $results;
}

// 2.3.1. Name Selector
// A name selector '<name>' selects at most one object member value.
function apply_member_name_selector($selector, $json)
{
	// Nothing is selected from a value that is not an object.
	if (!is_json_object($json)) {
		return [];
	}

	// Applying the name-selector to an object node
	// selects a member value whose name equals the member name M
	if (json_object_has_key($json, $selector->member)) {
		return [json_get($json, $selector->member)];
	}

	return [];
}

// 2.3.3. Index Selector
// An index selector <index> matches at most one array element value.
function apply_index_selector($node, $json)
{
	if (!is_json_array($json)) {
		return [];
	}

	$index = Array\normalize_index($node->index, sizeof($json));

	return $index === null ? [] : [$json[$index]];
}
