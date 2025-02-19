<?php

declare(strict_types=1);

use function Loilo\JsonPath\compare_objects;

describe('ObjectComparator', function () {
	describe('eq operator', function () {
		it('compare returns true for equal objects', function () {
			expect(
				compare_objects(
					'==',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(true);
		});

		it('compare returns false for different objects', function () {
			expect(
				compare_objects(
					'==',
					(object) ['a' => 'foo'],
					(object) ['a' => 'bar'],
				),
			)->toBe(false);
		});

		it(
			'compare returns false for different collection of names',
			function () {
				expect(
					compare_objects(
						'==',
						(object) ['a' => 'foo', 'b' => 'bar'],
						(object) ['a' => 'foo'],
					),
				)->toBe(false);
			},
		);

		it('compare returns true for equal nested objects', function () {
			expect(
				compare_objects(
					'==',
					(object) ['a' => (object) ['b' => 'foo']],
					(object) ['a' => (object) ['b' => 'foo']],
				),
			)->toBe(true);
		});

		it('compare returns false for different nested objects', function () {
			expect(
				compare_objects(
					'==',
					(object) ['a' => (object) ['b' => 'foo']],
					(object) ['a' => (object) ['b' => 'bar']],
				),
			)->toBe(false);
		});

		it(
			'compare returns false for different object with different type',
			function () {
				expect(
					compare_objects(
						'==',
						(object) ['a' => (object) ['b' => 1]],
						(object) ['a' => (object) ['b' => '1']],
					),
				)->toBe(false);
			},
		);
	});

	describe('neq operator', function () {
		it('compare returns false for equal objects', function () {
			expect(
				compare_objects(
					'!=',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(false);
		});
	});

	describe('lt operator', function () {
		it('does not defined for objects', function () {
			expect(
				compare_objects(
					'<',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(false);
		});
	});

	describe('lte operator', function () {
		it('eq implies lte', function () {
			expect(
				compare_objects(
					'<=',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(true);
		});
	});

	describe('gt operator', function () {
		it('does not defined for objects', function () {
			expect(
				compare_objects(
					'>',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(false);
		});
	});

	describe('gte operator', function () {
		it('eq implies gte', function () {
			expect(
				compare_objects(
					'>=',
					(object) ['a' => 'foo'],
					(object) ['a' => 'foo'],
				),
			)->toBe(true);
		});
	});
});
