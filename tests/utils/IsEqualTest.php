<?php

declare(strict_types=1);

use function Loilo\JsonPath\is_equal;

describe('isEqual', function () {
	test('should return true for equal primitive values', function () {
		expect(is_equal(1, 1))->toBe(true);
		expect(is_equal(1.2, 1.2))->toBe(true);
		expect(is_equal('a', 'a'))->toBe(true);
		expect(is_equal(true, true))->toBe(true);
		expect(is_equal(null, null))->toBe(true);
	});

	test('should return false for different primitive values', function () {
		expect(is_equal(1, 2))->toBe(false);
		expect(is_equal(1.2, 2.1))->toBe(false);
		expect(is_equal('a', 'b'))->toBe(false);
		expect(is_equal(true, false))->toBe(false);
		expect(is_equal(null, 1))->toBe(false);
	});

	test('should return true for equal arrays', function () {
		expect(is_equal([], []))->toBe(true);
		expect(is_equal([1, 2, 3], [1, 2, 3]))->toBe(true);
		expect(is_equal([1, 2, [3, 4]], [1, 2, [3, 4]]))->toBe(true);
	});

	test('should return false for different arrays', function () {
		expect(is_equal([], [1]))->toBe(false);
		expect(is_equal([1, 2, 3], [1, 2, 4]))->toBe(false);
		expect(is_equal([1, 2, [3, 4]], [1, 2, [3, 5]]))->toBe(false);
	});

	test('should return true for equal objects', function () {
		expect(is_equal((object) [], (object) []))->toBe(true);
		expect(
			is_equal(
				(object) ['a' => 1, 'b' => 2],
				(object) ['a' => 1, 'b' => 2],
			),
		)->toBe(true);
	});

	test('should return true for nested equal objects', function () {
		expect(
			is_equal(
				(object) ['a' => 1, 'b' => (object) ['c' => '2']],
				(object) ['a' => 1, 'b' => (object) ['c' => '2']],
			),
		)->toBe(true);
	});

	test('should return false for different objects', function () {
		expect(is_equal((object) [], (object) ['a' => 1]))->toBe(false);
		expect(
			is_equal(
				(object) ['a' => 1, 'b' => 2],
				(object) ['a' => 1, 'b' => 3],
			),
		)->toBe(false);
	});

	test(
		'should return true for object that has circular dependency',
		function () {
			$a = (object) ['foo' => 'bar'];
			$b = (object) ['foo' => 'bar'];

			$a->self = $a;
			$b->self = $b;

			expect(is_equal($a, $b))->toBe(true);
		},
	);

	test(
		'should return false for different object that has circular dependency',
		function () {
			$a_child = (object) ['baz' => 'qux'];
			$b_child = (object) ['baz' => 'quux'];

			$a_child->self = $a_child;
			$b_child->self = $b_child;

			$a = (object) ['foo' => 'bar', 'child' => $a_child];
			$b = (object) ['foo' => 'bar', 'child' => $b_child];

			expect(is_equal($a, $b))->toBe(false);
		},
	);
});
