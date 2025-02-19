<?php

namespace Loilo\JsonPath;

// equal arrays, that is, arrays of the same length where each element of
// the first array is equal to the corresponding element of the second array, or
function compare_arrays($operator, $a, $b)
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
			throw new \InvalidArgumentException("Unsupported operator: $operator");
	}
}
