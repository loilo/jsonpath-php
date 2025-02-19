<?php

namespace Loilo\JsonPath;

/**
 * Check the deep equality of two JSON values.
 * @param mixed $a The first JSON value.
 * @param mixed $b The second JSON value.
 * @return bool `true` if the two JSON values are equal, `false` otherwise.
 */
function is_equal($a, $b): bool
{
	return is_equal_impl($a, $b);
}

function is_equal_impl($a, $b, $visited = null): bool
{
	if ($visited === null) {
		$visited = class_exists('WeakMap') ? new \WeakMap() : new \SplObjectStorage();
	}

	if ($a === $b) {
		return true;
	}

	if (gettype($a) !== gettype($b)) {
		return false;
	}

	if ($a === null || $b === null) {
		return false;
	}

	if (is_json_array($a) && is_json_array($b)) {
		if (sizeof($a) !== sizeof($b)) {
			return false;
		}

		foreach ($a as $index => $value) {
			if (!is_equal_impl($value, $b[$index], $visited)) {
				return false;
			}
		}

		return true;
	}

	if (is_json_array($a) || is_json_array($b)) {
		return false;
	}

	if (is_object($a) && is_object($b)) {
		// Check circular references
		if (isset($visited[$a])) {
			return $visited[$a] === $b;
		}

		$visited[$a] = $b;

		$keys_a = array_keys(get_object_vars($a));
		$keys_b = array_keys(get_object_vars($b));

		if (sizeof($keys_a) !== sizeof($keys_b)) {
			return false;
		}

		foreach ($keys_a as $key) {
			if (!is_equal_impl($a->$key, $b->$key, $visited)) {
				return false;
			}
		}

		return true;
	}

	return false;
}
