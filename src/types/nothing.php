<?php

namespace Loilo\JsonPath;

function nothing()
{
	static $value = null;

	if ($value === null) {
		$value = (object) ['type' => 'Nothing'];
	}

	return $value;
}
