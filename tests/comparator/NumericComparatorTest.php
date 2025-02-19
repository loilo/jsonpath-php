<?php

declare(strict_types=1);

use function Loilo\JsonPath\compare_numbers;

describe('NumericComparator', function () {
	it('eq returns true for equal number', function () {
		expect(compare_numbers('==', 1, 1))->toBe(true);
	});

	it('eq returns false for different numbers', function () {
		expect(compare_numbers('==', 1, 2))->toBe(false);
	});

	it('neq returns true for different numbers', function () {
		expect(compare_numbers('!=', 1, 2))->toBe(true);
	});

	it('neq returns false for equal numbers', function () {
		expect(compare_numbers('!=', 1, 1))->toBe(false);
	});

	it('lt returns true for a < b', function () {
		expect(compare_numbers('<', 1, 2))->toBe(true);
	});

	it('lt returns false for a >= b', function () {
		expect(compare_numbers('<', 2, 1))->toBe(false);
	});

	it('lte returns true for a <= b', function () {
		expect(compare_numbers('<=', 1, 1))->toBe(true);
		expect(compare_numbers('<=', 1, 2))->toBe(true);
	});

	it('lte returns false for a > b', function () {
		expect(compare_numbers('<=', 2, 1))->toBe(false);
	});

	it('gt returns true for a > b', function () {
		expect(compare_numbers('>', 2, 1))->toBe(true);
	});

	it('gt returns false for a <= b', function () {
		expect(compare_numbers('>', 1, 2))->toBe(false);
	});

	it('gte returns true for a >= b', function () {
		expect(compare_numbers('>=', 1, 1))->toBe(true);
		expect(compare_numbers('>=', 2, 1))->toBe(true);
	});

	it('gte returns false for a < b', function () {
		expect(compare_numbers('>=', 1, 2))->toBe(false);
	});
});
