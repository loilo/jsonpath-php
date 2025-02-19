<?php

declare(strict_types=1);

use function Loilo\JsonPath\nothing;
use function Loilo\JsonPath\eval_compare;

describe('2.3.5.2.2. Comparisons', function () {
	describe('2.3.5.3. Examples', function () {
		$obj = (object) ['json' => (object) ['x' => 'y']];
		$array = (object) ['json' => [2, 3]];

		it('Empty nodelists 1', function () {
			expect(eval_compare(nothing(), nothing(), '=='))->toBe(true);
		});

		it('equals implies lte 1', function () {
			expect(eval_compare(nothing(), nothing(), '<='))->toBe(true);
		});

		it('Empty nodelist 1', function () {
			expect(
				eval_compare(nothing(), (object) ['json' => 'g'], '=='),
			)->toBe(false);
		});

		it('Empty nodelists 2', function () {
			expect(eval_compare(nothing(), nothing(), '!='))->toBe(false);
		});

		it('Empty nodelist 2', function () {
			expect(
				eval_compare(nothing(), (object) ['json' => 'g'], '!='),
			)->toBe(true);
		});

		it('Numeric comparison', function () {
			expect(eval_compare(1, 2, '<='))->toBe(true);
		});

		it('Strict, numeric comparison', function () {
			expect(eval_compare(1, 2, '>'))->toBe(false);
		});

		it('Type mismatch 1', function () {
			expect(eval_compare(13, '13', '=='))->toBe(false);
		});

		it('String comparison', function () {
			expect(eval_compare('a', 'b', '<='))->toBe(true);
		});

		it('Strict, string comparison', function () {
			expect(eval_compare('a', 'b', '>'))->toBe(false);
		});

		it('Type mismatch 2', function () use ($obj, $array) {
			expect(eval_compare($obj, $array, '=='))->toBe(false);
		});

		it('Type mismatch 3', function () use ($obj, $array) {
			expect(eval_compare($obj, $array, '!='))->toBe(true);
		});

		it('Object comparison 1', function () use ($obj) {
			expect(eval_compare($obj, $obj, '=='))->toBe(true);
		});

		it('Object comparison 2', function () use ($obj) {
			expect(eval_compare($obj, $obj, '!='))->toBe(false);
		});

		it('Array comparison 1', function () use ($array) {
			expect(eval_compare($array, $array, '=='))->toBe(true);
		});

		it('Array comparison 2', function () use ($array) {
			expect(eval_compare($array, $array, '!='))->toBe(false);
		});

		it('Type mismatch 4', function () use ($obj) {
			expect(eval_compare($obj, 17, '=='))->toBe(false);
		});

		it('Type mismatch 5', function () use ($obj) {
			expect(eval_compare($obj, 17, '!='))->toBe(true);
		});

		it('Objects and arrays do not offer lte comparison', function () use (
			$obj,
			$array
		) {
			expect(eval_compare($obj, $array, '<='))->toBe(false);
		});

		it('Objects and arrays do not offer lt comparison', function () use (
			$obj,
			$array
		) {
			expect(eval_compare($obj, $array, '<'))->toBe(false);
		});

		it('equals implies lte 2', function () use ($obj) {
			expect(eval_compare($obj, $obj, '<='))->toBe(true);
		});

		it('equals implies lte 3', function () use ($array) {
			expect(eval_compare($array, $array, '<='))->toBe(true);
		});

		it('Arrays do not offer lte comparison', function () use ($array) {
			expect(eval_compare(1, $array, '<='))->toBe(false);
		});

		it('Arrays do not offer gte comparison', function () use ($array) {
			expect(eval_compare(1, $array, '>='))->toBe(false);
		});

		it('Arrays do not offer gt comparison', function () use ($array) {
			expect(eval_compare(1, $array, '>'))->toBe(false);
		});

		it('Arrays do not offer lt comparison', function () use ($array) {
			expect(eval_compare(1, $array, '<'))->toBe(false);
		});

		it('equals implies lte 4', function () {
			expect(eval_compare(true, true, '<='))->toBe(true);
		});

		it('Booleans do not offer gt comparison', function () {
			expect(eval_compare(true, true, '>'))->toBe(false);
		});
	});
});
