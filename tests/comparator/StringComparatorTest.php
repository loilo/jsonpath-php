<?php

declare(strict_types=1);

use function Loilo\JsonPath\compare_strings;

describe('StringComparator', function () {
	it('eq returns true for equal strings', function () {
		expect(compare_strings('==', 'hello', 'hello'))->toBe(true);
	});

	it('eq returns false for different strings', function () {
		expect(compare_strings('==', 'hello', 'world'))->toBe(false);
	});

	it('neq returns true for different strings', function () {
		expect(compare_strings('!=', 'hello', 'world'))->toBe(true);
	});

	it('neq returns false for equal strings', function () {
		expect(compare_strings('!=', 'hello', 'hello'))->toBe(false);
	});

	it('lt returns true for a < b', function () {
		expect(compare_strings('<', 'a', 'b'))->toBe(true);
	});

	it('lt returns false for a >= b', function () {
		expect(compare_strings('<', 'b', 'a'))->toBe(false);
	});

	it('lte returns true for a <= b', function () {
		expect(compare_strings('<=', 'a', 'b'))->toBe(true);
		expect(compare_strings('<=', 'a', 'a'))->toBe(true);
	});

	it('lte returns false for a > b', function () {
		expect(compare_strings('<=', 'b', 'a'))->toBe(false);
	});

	it('gt returns true for a > b', function () {
		expect(compare_strings('>', 'b', 'a'))->toBe(true);
	});

	it('gt returns false for a <= b', function () {
		expect(compare_strings('>', 'a', 'b'))->toBe(false);
	});

	it('gte returns true for a >= b', function () {
		expect(compare_strings('>=', 'b', 'a'))->toBe(true);
		expect(compare_strings('>=', 'a', 'a'))->toBe(true);
	});

	it('gte returns false for a < b', function () {
		expect(compare_strings('>=', 'a', 'b'))->toBe(false);
	});
});
