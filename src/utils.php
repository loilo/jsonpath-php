<?php

namespace Loilo\JsonPath;

/**
 * Check whether a JSON object has a key
 */
function json_object_has_key($value, $key): bool
{
	return is_array($value) ? array_key_exists($key, $value) : property_exists($value, $key);
}

/**
 * Get a value from a JSON object by key
 */
function json_get($value, $key)
{
	return is_array($value) ? $value[$key] ?? null : $value->$key ?? null;
}

/**
 * Check if a value is a JSON object like structure
 */
function is_json_object($value): bool
{
	return (is_array($value) && !Array\is_list($value)) || $value instanceof \stdClass;
}

/**
 * Check if a value is a JSON primitive
 */
function is_json_primitive($value): bool
{
	return is_scalar($value) || is_null($value);
}

/**
 * Check if a value is a JSON array (list)
 */
function is_json_array($value): bool
{
	return is_array($value) && Array\is_list($value);
}

/**
 * Convert a value JSON object or array to a JSON array
 */
function to_array($value): array
{
	if (is_json_array($value)) {
		return $value;
	}

	return map_json_object($value);
}

/**
 * Convert a JSON object to a JSON array
 */
function map_json_object($value): array
{
	return array_values((array) $value);
}
