<?php

namespace Loilo\JsonPath;

// equal objects with no duplicate names, that is, where:
// both objects have the same collection of names (with no duplicates) and
// for each of those names, the values associated with the name by the objects are equal.
function compare_objects($operator, $a, $b)
{
	switch ($operator) {
		case '==':
			return is_equal($a, $b);
		case '!=':
			return !is_equal($a, $b);
		case '<':
			// Not defined
			return false;
		case '<=':
			return is_equal($a, $b);
		case '>':
			// Not defined
			return false;
		case '>=':
			return is_equal($a, $b);
		default:
			throw new \InvalidArgumentException("Invalid comparison operator: $operator");
	}
}
