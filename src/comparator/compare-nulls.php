<?php

namespace Loilo\JsonPath;

function compare_nulls($operator, $a, $b)
{
	return match ($operator) {
		'==' => $a === $b,
		'!=' => $a !== $b,
		'<' => false,
		'<=' => $a === $b,
		'>' => false,
		'>=' => $a === $b,
		default => throw new \InvalidArgumentException("Invalid comparison operator: $operator"),
	};
}
