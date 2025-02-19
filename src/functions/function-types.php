<?php

namespace Loilo\JsonPath;

class LogicalType
{
	static function true()
	{
		static $true;
		if ($true === null) {
			$true = new LogicalType();
		}
		return $true;
	}

	static function false()
	{
		static $false;
		if ($false === null) {
			$false = new LogicalType();
		}
		return $false;
	}
}

function convert_logical_type($value)
{
	if ($value) {
		return LogicalType::true();
	}
	return LogicalType::false();
}

function is_logical_type($value): bool
{
	return $value === LogicalType::true() || $value === LogicalType::false();
}
