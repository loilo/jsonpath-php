<?php

namespace Loilo\JsonPath;

// RFC 9485 I-Regexp: An Interoperable Regular Expression Format
// www.rfc-editor.org/rfc/rfc9485.html
//
// 5.3. ECMAScript Regexps
// Perform the following steps on an I-Regexp to obtain a PHP regexp:
//
// For any unescaped dots (.) outside character classes (first alternative of charClass production), replace the dot with [^\n\r].
// Envelope the result in ^(?: and )$.
// The PHP regexp is to be interpreted as a Unicode pattern.
//
// Note that where a regexp literal is required, the actual regexp needs to be enclosed in delimiters.
function convert_i_regexp_to_php_regexp(string $pattern): string
{
	$result = '';
	$in_char_class = false;
	$in_escape = false;

	for ($i = 0; $i < mb_strlen($pattern); $i++) {
		$c = mb_substr($pattern, $i, 1);

		if ($in_escape) {
			$result .= "\\{$c}";
			$in_escape = false;
			continue;
		}

		if ($c === '\\') {
			$in_escape = true;
			continue;
		}

		if ($c === '[') {
			$in_char_class = true;
			$result .= $c;
			continue;
		}

		if ($c === ']') {
			$in_char_class = false;
			$result .= $c;
			continue;
		}

		if ($c === '.' && !$in_char_class && !$in_escape) {
			$result .= '[^\n\r]';
		} else {
			$result .= $c;
		}
	}

	if ($in_escape) {
		throw new \Exception('Invalid I-Regexp: ends with a backslash escape.');
	}

	return $result;
}
