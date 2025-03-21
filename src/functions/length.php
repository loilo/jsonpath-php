<?php

namespace Loilo\JsonPath;

// 2.4.4. length() Function Extension
// Parameters:
//   ValueType
// Result:
//   ValueType (unsigned integer or Nothing)
//
// The length() function extension provides a way to compute the length of a value
// and make that available for further processing in the filter expression:
function length_function(): FunctionDefinition
{
	static $length_function = null;

	if ($length_function === null) {
		$length_function = new FunctionDefinition(
			'length',
			[new ValueTypeDef()],
			new ValueTypeDef(),
			function ($node) {
				if ($node === nothing()) {
					return nothing();
				}

				// If the argument value is a string, the result is the number of Unicode scalar values in the string.
				if (is_string($node)) {
					return mb_strlen($node);
				}

				// If the argument value is an array, the result is the number of elements in the array.
				if (is_json_array($node)) {
					return sizeof($node);
				}

				// If the argument value is an object, the result is the number of members in the object.
				if (is_json_object($node)) {
					return sizeof(is_array($node) ? $node : get_object_vars($node));
				}

				// For any other argument value, the result is the special result Nothing.
				return nothing();
			},
		);
	}

	return $length_function;
}
