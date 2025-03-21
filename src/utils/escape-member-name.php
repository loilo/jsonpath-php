<?php

namespace Loilo\JsonPath;

function escape_member_name(string $name): string
{
	return preg_replace_callback(
		'/[\'\\\\\b\f\n\r\t\x00-\x1F]/',
		function ($matches) {
			$char = $matches[0];
			switch ($char) {
				case "'":
					return "\\'";
				case '\\':
					return '\\\\';
				case '\b':
					return '\\b';
				case "\f":
					return "\\f";
				case "\n":
					return "\\n";
				case "\r":
					return "\\r";
				case "\t":
					return "\\t";
				default:
					return sprintf('\\u%04x', ord($char));
			}
		},
		$name,
	);
}
