<?php

namespace Loilo\JsonPath;

// 2.4.6. match() Function Extension
// Parameters:
//  ValueType (string)
//  ValueType (string conforming to [RFC9485])
// Result:
//  LogicalType
// The match() function extension provides a way to check whether (the entirety of; see Section 2.4.7)
// a given string matches a given regular expression, which is in the form described in [RFC9485].
function match_function(): FunctionDefinition
{
	static $match_function = null;

	if ($match_function === null) {
		$match_function = new FunctionDefinition(
			'match',
			[new ValueTypeDef(), new ValueTypeDef()],
			new LogicalTypeDef(),
			function ($node, $i_regexp_pattern) {
				if (!is_string($node) || !is_string($i_regexp_pattern)) {
					return LogicalType::false();
				}

				// Perform the following steps on an I-Regexp to obtain an ECMAScript regexp [ECMA-262]:
				//
				// For any unescaped dots (.) outside character classes (first alternative of charClass production), replace the dot with [^\n\r].
				// Envelope the result in ^(?: and )$.
				// The ECMAScript regexp is to be interpreted as a Unicode pattern ("u" flag; see Section 21.2.2 "Pattern Semantics" of [ECMA-262]).
				//
				// Note that where a regexp literal is required, the actual regexp needs to be enclosed in /.
				$php_script_regex_pattern = convert_i_regexp_to_php_regexp($i_regexp_pattern);
				$test_result = preg_match(
					'/^(?:' . str_replace('/', '\\/', $php_script_regex_pattern) . ')$/u',
					$node,
				);
				return convert_logical_type($test_result === 1);
			},
		);
	}

	return $match_function;
}
