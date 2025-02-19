<?php

namespace Loilo\JsonPath;

// 2.4.7. search() Function Extension
// Parameters:
//  ValueType (string)
//  ValueType (string conforming to [RFC9485])
// Result:
//  LogicalType
//
// The search() function extension provides a way to check whether a given string
// contains a substring that matches a given regular expression,
// which is in the form described in [RFC9485].
function search_function(): FunctionDefinition
{
	static $search_function = null;

	if ($search_function === null) {
		$search_function = new FunctionDefinition(
			'search',
			[new ValueTypeDef(), new ValueTypeDef()],
			new LogicalTypeDef(),
			function ($node, $i_regexp_pattern) {
				if (!is_string($node) || !is_string($i_regexp_pattern)) {
					return LogicalType::false();
				}

				$php_script_regex_pattern = convert_i_regexp_to_php_regexp($i_regexp_pattern);
				$test_result = preg_match(
					'/' . str_replace('/', '\\/', $php_script_regex_pattern) . '/u',
					$node,
				);
				return convert_logical_type($test_result === 1);
			},
		);
	}

	return $search_function;
}
