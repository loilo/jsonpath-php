<?php

namespace Loilo\JsonPath;

// 2.4.8. value() Function Extension
// Parameters:
//  NodesType
// Result:
//  ValueType
//
// The value() function extension provides a way to convert an instance of NodesType
// to a value and make that available for further processing in the filter expression:
function value_function(): FunctionDefinition
{
	static $value_function = null;

	if ($value_function === null) {
		$value_function = new FunctionDefinition(
			'value',
			[new NodesTypeDef()],
			new ValueTypeDef(),
			function ($nodes) {
				// Its only argument is an instance of NodesType (possibly taken from a filter-query, as in the example above). The result is an instance of ValueType.
				// If the argument contains a single node, the result is the value of the node.
				if (sizeof($nodes) === 1) {
					return $nodes[0]->value;
				}

				// If the argument is the empty nodelist or contains multiple nodes, the result is Nothing.
				return nothing();
			},
		);
	}

	return $value_function;
}
