<?php

namespace Loilo\JsonPath;

/**
 * Map a callback over the elements of one or more arrays and flatten the result
 */
function array_flat_map(callable $callback, ...$arrays): array
{
	if (sizeof($arrays) === 0) {
		return [];
	}

	$new_array = [];
	$maxlength = max(array_map('sizeof', $arrays));
	for ($i = 0; $i < $maxlength; $i++) {
		$args =
			sizeof($arrays) === 1
				? [$arrays[0][$i]]
				: array_map(fn($array) => $array[$i] ?? null, $arrays);
		$result = call_user_func_array($callback, $args);

		if (is_array($result)) {
			if (empty($result)) {
				continue;
			}

			$new_array = array_merge($new_array, $result);
		} else {
			$new_array[] = $result;
		}
	}

	return $new_array;
}

/**
 * Flatten a multi-dimensional array by a given number of levels
 */
function array_flatten(array $array, float $levels = 1): array
{
	if ($levels <= 0) {
		return $array;
	}

	$result = [];
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$result = array_merge($result, array_flatten($value, $levels - 1));
		} else {
			$result = array_merge($result, [$key => $value]);
		}
	}

	return $result;
}

/**
 * Filter an array and re-indexes it
 */
function array_filter_2(array $array, ?callable $callback = null): array
{
	return array_values(array_filter($array, $callback));
}

/**
 * Check if an array is a list
 */
function array_is_list_2(array $array): bool
{
	if (function_exists('array_is_list')) {
		return \array_is_list($array);
	}

	return array_keys($array) === range(0, count($array) - 1);
}

/**
 * Check if a value is a number
 */
function is_number($value): bool
{
	return is_int($value) || is_float($value);
}

/**
 * Normalize an index for a given array length to be positive
 * or null if it is out of bounds
 * An index is out of bounds if it is less than the negative length
 * or greater than or equal to the length
 */
function normalize_index($index, int $length): ?int
{
	if ($index < -$length || $index >= $length) {
		return null;
	}

	if ($index < 0) {
		$index += $length;
	}

	return $index;
}
