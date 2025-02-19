<?php

declare(strict_types=1);

use function Loilo\JsonPath\compare_arrays;

describe('ArrayComparator', function () {
	describe('== operator', function () {
		it('compare returns true for equal arrays', function () {
			expect(compare_arrays('==', [1], [1]))->toBe(true);
		});

		it('compare returns false for different arrays', function () {
			expect(compare_arrays('==', [1], [2]))->toBe(false);
		});

		it('compare returns false for different collections', function () {
			expect(compare_arrays('==', [1, 2], [1]))->toBe(false);
		});

		it('compare returns true for equal nested objects', function () {
			expect(compare_arrays('==', [1, [2]], [1, [2]]))->toBe(true);
		});

		it('compare returns false for different nested objects', function () {
			expect(compare_arrays('==', [1, [2]], [1, [3]]))->toBe(false);
		});

		it(
			'compare returns false for different object with different type',
			function () {
				expect(compare_arrays('==', [1, 2], [1, '2']))->toBe(false);
			},
		);
	});

	describe('neq operator', function () {
		it('compare returns false for equal objects', function () {
			expect(compare_arrays('!=', [1], [1]))->toBe(false);
		});
	});

	describe('lt operator', function () {
		it('does not defined for objects lt', function () {
			expect(compare_arrays('<', [1], [1]))->toBe(false);
		});
	});

	describe('lte operator', function () {
		it('== implies <=', function () {
			expect(compare_arrays('<=', [1], [1]))->toBe(true);
		});
	});

	describe('gt operator', function () {
		it('does not defined for objects gt', function () {
			expect(compare_arrays('>', [1], [1]))->toBe(false);
		});
	});

	describe('gte operator', function () {
		it('== implies >=', function () {
			expect(compare_arrays('>=', [1], [1]))->toBe(true);
		});
	});
});
