<?php

namespace Loilo\JsonPath;

// equal arrays, that is, arrays of the same length where each element of
// the first array is equal to the corresponding element of the second array, or
function compare_arrays($operator, $a, $b)
{
	return match ($operator) {
		'==' => is_equal($a, $b),
		'!=' => !is_equal($a, $b),
		'<' => false, // Not defined
		'<=' => is_equal($a, $b),
		'>' => false, // Not defined
		'>=' => is_equal($a, $b),
		default => throw new \InvalidArgumentException("Unsupported operator: $operator"),
	};
}
