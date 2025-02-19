<?php

use Loilo\JsonPath\JsonPath;
use Loilo\JsonPath\SyntaxError;

test('Query that does not start with $ should throw an error', function () {
	expect(fn() => new JsonPath('*'))->toThrow(SyntaxError::class);
});

test('Query with unclosed brackets should throw an error', function () {
	expect(fn() => new JsonPath('$.store.book[0'))->toThrow(SyntaxError::class);
});
