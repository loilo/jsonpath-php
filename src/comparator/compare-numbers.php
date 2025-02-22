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

	return match ($operator) {
		'==' => $a === $b,
		'!=' => $a !== $b,
		'<' => is_number($b) && $a < $b,
		'<=' => is_number($b) && $a <= $b,
		'>' => is_number($b) && $a > $b,
		'>=' => is_number($b) && $a >= $b,
		default => throw new \InvalidArgumentException("Invalid comparison operator: $operator"),
	};
}
