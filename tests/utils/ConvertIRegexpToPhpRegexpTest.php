<?php

declare(strict_types=1);

use function Loilo\JsonPath\convert_i_regexp_to_php_regexp;

describe('convertIRegexpToPhpRegexp', function () {
	test(
		'For any unescaped dots (.) outside character classes replace the dot with [^\n\r]',
		function () {
			$iregexp_pattern_string = 'a';
			$result = convert_i_regexp_to_php_regexp($iregexp_pattern_string);
			expect($result)->toBe('a');
		},
	);

	test('dot in character class should not be replaced', function () {
		$iregexp_pattern_string = 'a[.b]c';
		$result = convert_i_regexp_to_php_regexp($iregexp_pattern_string);
		expect($result)->toBe('a[.b]c');
	});

	test('should escape backslashes', function () {
		$iregexp_pattern_string = 'a\\.c';
		$result = convert_i_regexp_to_php_regexp($iregexp_pattern_string);
		expect($result)->toBe('a\\.c');
	});

	test('should replace dot', function () {
		$iregexp_pattern_string = 'a.c';
		$result = convert_i_regexp_to_php_regexp($iregexp_pattern_string);
		expect($result)->toBe('a[^\\n\\r]c');
	});
});
