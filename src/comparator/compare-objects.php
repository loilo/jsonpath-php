<?php

namespace Loilo\JsonPath;

// equal objects with no duplicate names, that is, where:
// both objects have the same collection of names (with no duplicates) and
// for each of those names, the values associated with the name by the objects are equal.
function compare_objects($operator, $a, $b)
{
	return match ($operator) {
		'==' => is_equal($a, $b),
		'!=' => !is_equal($a, $b),
		'<' => false, // Not defined
		'<=' => is_equal($a, $b),
		'>' => false, // Not defined
		'>=' => is_equal($a, $b),
		default => throw new \InvalidArgumentException("Invalid comparison operator: $operator"),
	};
}
