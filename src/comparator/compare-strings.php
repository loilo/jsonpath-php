<?php

namespace Loilo\JsonPath;

// a non-empty string compares less than another non-empty string
// if and only if the first string starts with a lower Unicode scalar
// value than the second string or if both strings start with the same
// Unicode scalar value and the remainder of the first string compares
// less than the remainder of the second string.
function compare_strings($operator, $a, $b)
{
	switch ($operator) {
		case '==':
			return $a === $b;
		case '!=':
			return $a !== $b;
		case '<':
			return $a < $b;
		case '<=':
			return $a <= $b;
		case '>':
			return $a > $b;
		case '>=':
			return $a >= $b;
		default:
			throw new \InvalidArgumentException("Invalid comparison operator: $operator");
	}
}
