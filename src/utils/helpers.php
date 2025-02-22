<?php

namespace Loilo\JsonPath;

/**
 * Check if a value is a number
 */
function is_number($value): bool
{
	return is_int($value) || is_float($value);
}
