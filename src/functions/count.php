<?php

namespace Loilo\JsonPath;

// 2.4.5. count() Function Extension
// Parameters:
//  NodesType
// Result:
//  ValueType (unsigned integer)
//
// The count() function extension provides a way to obtain the number of nodes
// in a nodelist and make that available for further processing in the filter expression:
function count_function(): FunctionDefinition
{
	static $count_function = null;

	if ($count_function === null) {
		$count_function = new FunctionDefinition(
			'length',
			[new NodesTypeDef()],
			new ValueTypeDef(),
			function ($nodes) {
				return sizeof($nodes);
			},
		);
	}

	return $count_function;
}
