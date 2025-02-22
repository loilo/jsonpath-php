<?php

namespace Loilo\JsonPath;

// When either side of a comparison results in an empty nodelist or the special result nothing()
// - A comparison using the operator == yields true if and only the other side also results
//   in an empty nodelist or the special result nothing().
// - A comparison using the operator < yields false.
function compare_nodes($operator, $a, $b)
{
	$nothing = nothing();

	return match ($operator) {
		'==' => ($a === $nothing || $b === $nothing) && $a === $b,
		'!=' => ($a === $nothing || $b === $nothing) && $a !== $b,
		'<' => false, // Not defined
		'<=' => ($a === $nothing || $b === $nothing) && $a === $b,
		'>' => false, // Not defined
		'>=' => ($a === $nothing || $b === $nothing) && $a === $b,
		default => throw new \InvalidArgumentException("Invalid comparison operator: $operator"),
	};
}
