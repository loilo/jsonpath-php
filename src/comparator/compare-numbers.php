<?php

namespace Loilo\JsonPath;

function compare_numbers($operator, $a, $b)
{
	// Convert numbers to float to allow comparison of integers and floats
	if (is_number($a)) {
		$a = floatval($a);
	}

	if (is_number($b)) {
		$b = floatval($b);
	}

	switch ($operator) {
		case '==':
			return $a === $b;
		case '!=':
			return $a !== $b;
		case '<':
			if (!is_number($b)) {
				return false;
			}
			return $a < $b;
		case '<=':
			if (!is_number($b)) {
				return false;
			}
			return $a <= $b;
		case '>':
			if (!is_number($b)) {
				return false;
			}
			return $a > $b;
		case '>=':
			if (!is_number($b)) {
				return false;
			}
			return $a >= $b;
		default:
			throw new \InvalidArgumentException("Invalid comparison operator: $operator");
	}
}
