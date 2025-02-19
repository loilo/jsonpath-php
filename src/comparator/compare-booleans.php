<?php

namespace Loilo\JsonPath;

function compare_booleans($operator, $a, $b)
{
	switch ($operator) {
		case '==':
			return $a === $b;
		case '!=':
			return $a !== $b;
		case '<':
			return false;
		case '<=':
			return $a === $b;
		case '>':
			return false;
		case '>=':
			return $a === $b;
		default:
			throw new \InvalidArgumentException("Invalid operator: $operator");
	}
}
