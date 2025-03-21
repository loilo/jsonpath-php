<?php

declare(strict_types=1);

require_once __DIR__ . '/../utils.php';

describe('RFC 9535 JSONPath: Query Expressions for JSON', function () {
	// https://www.rfc-editor.org/rfc/rfc9535.html#name-root-identifier
	describe('2.2. Root Identifier', function () {
		$json = (object) ['k' => 'v'];
		it('Root node', function () use ($json) {
			test_json_path($json, '$', [$json]);
		});
	});

	describe('2.3. Selectors', function () {
		// https://www.rfc-editor.org/rfc/rfc9535.html#name-name-selector
		describe('2.3.1. Name Selector', function () {
			$json = (object) [
				'o' => (object) ['j j' => (object) ['k.k' => 3]],
				"'" => (object) ['@' => 2],
			];

			it('Named value in a nested object', function () use ($json) {
				test_json_path($json, "$.o['j j']", [(object) ['k.k' => 3]]);
			});

			it('Nesting further down', function () use ($json) {
				test_json_path($json, "$.o['j j']['k.k']", [3]);
			});

			it(
				'Different delimiter in the query, unchanged Normalized Path',
				function () use ($json) {
					test_json_path($json, '$.o["j j"]["k.k"]', [3]);
				},
			);

			it('Unusual member names', function () use ($json) {
				test_json_path($json, "$['\'']['@']", [2]);
			});
		});

		// https://www.rfc-editor.org/rfc/rfc9535.html#name-wildcard-selector
		describe('2.3.2. Wildcard Selector', function () {
			$json = (object) [
				'o' => (object) ['j' => 1, 'k' => 2],
				'a' => [5, 3],
			];

			it('Object values 1', function () use ($json) {
				test_json_path($json, '$[*]', [
					(object) ['j' => 1, 'k' => 2],
					[5, 3],
				]);
			});

			it('Object values 2', function () use ($json) {
				test_json_path($json, '$.o[*]', [1, 2]);
			});

			it('Non-deterministic ordering', function () use ($json) {
				test_json_path($json, '$.o[*, *]', [1, 2, 1, 2]);
			});

			it('Array members', function () use ($json) {
				test_json_path($json, '$.a[*]', [5, 3]);
			});
		});

		// https://www.rfc-editor.org/rfc/rfc9535.html#name-index-selector
		describe('2.3.3. Index Selector', function () {
			$json = ['a', 'b'];

			it('Element of array', function () use ($json) {
				test_json_path($json, '$[1]', ['b']);
			});

			it('Element of array, from the end', function () use ($json) {
				test_json_path($json, '$[-2]', ['a']);
			});
		});

		// https://www.rfc-editor.org/rfc/rfc9535.html#name-array-slice-selector
		describe('2.3.4. Array Slice Selector', function () {
			$json = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];

			it('Slice with default step', function () use ($json) {
				test_json_path($json, '$[1:3]', ['b', 'c']);
			});

			it('Slice with no end index', function () use ($json) {
				test_json_path($json, '$[5:]', ['f', 'g']);
			});

			it('Slice with step 2', function () use ($json) {
				test_json_path($json, '$[1:5:2]', ['b', 'd']);
			});

			it('Slice with negative step', function () use ($json) {
				test_json_path($json, '$[5:1:-2]', ['f', 'd']);
			});

			it('Slice in reverse order', function () use ($json) {
				test_json_path($json, '$[::-1]', [
					'g',
					'f',
					'e',
					'd',
					'c',
					'b',
					'a',
				]);
			});
		});

		// https://www.rfc-editor.org/rfc/rfc9535.html#name-filter-selector
		describe('2.3.5. Filter Selector', function () {
			$json = (object) [
				'a' => [
					3,
					5,
					1,
					2,
					4,
					6,
					(object) ['b' => 'j'],
					(object) ['b' => 'k'],
					(object) ['b' => (object) []],
					(object) ['b' => 'kilo'],
				],
				'o' => (object) [
					'p' => 1,
					'q' => 2,
					'r' => 3,
					's' => 5,
					't' => (object) ['u' => 6],
				],
				'e' => 'f',
			];

			it('Member value comparison', function () use ($json) {
				test_json_path($json, "$.a[?@.b == 'kilo']", [
					(object) ['b' => 'kilo'],
				]);
			});

			it('Equivalent query with enclosing parentheses', function () use (
				$json
			) {
				test_json_path($json, "$.a[?(@.b == 'kilo')]", [
					(object) ['b' => 'kilo'],
				]);
			});

			it('Array value comparison', function () use ($json) {
				test_json_path($json, '$.a[?@>3.5]', [5, 4, 6]);
			});

			it('Array value existence', function () use ($json) {
				test_json_path($json, '$.a[?@.b]', [
					(object) ['b' => 'j'],
					(object) ['b' => 'k'],
					(object) ['b' => (object) []],
					(object) ['b' => 'kilo'],
				]);
			});

			it('Existence of non-singular queries', function () use ($json) {
				test_json_path($json, '$[?@.*]', [
					[
						3,
						5,
						1,
						2,
						4,
						6,
						(object) ['b' => 'j'],
						(object) ['b' => 'k'],
						(object) ['b' => (object) []],
						(object) ['b' => 'kilo'],
					],
					(object) [
						'p' => 1,
						'q' => 2,
						'r' => 3,
						's' => 5,
						't' => (object) ['u' => 6],
					],
				]);
			});

			it('Nested filters', function () use ($json) {
				test_json_path($json, '$[?@[?@.b]]', [
					[
						3,
						5,
						1,
						2,
						4,
						6,
						(object) ['b' => 'j'],
						(object) ['b' => 'k'],
						(object) ['b' => (object) []],
						(object) ['b' => 'kilo'],
					],
				]);
			});

			it('Non-deterministic ordering', function () use ($json) {
				test_json_path_ignoring_array_order($json, '$.o[?@<3, ?@<3]', [
					1,
					2,
					2,
					1,
				]);
			});

			it('Array value logical OR', function () use ($json) {
				test_json_path($json, '$.a[?@<2 || @.b == "k"]', [
					1,
					(object) ['b' => 'k'],
				]);
			});

			it('Array value regular expression match', function () use ($json) {
				test_json_path($json, '$.a[?match(@.b, "[jk]")]', [
					(object) ['b' => 'j'],
					(object) ['b' => 'k'],
				]);
			});

			it('Array value regular expression search', function () use (
				$json
			) {
				test_json_path($json, '$.a[?search(@.b, "[jk]")]', [
					(object) ['b' => 'j'],
					(object) ['b' => 'k'],
					(object) ['b' => 'kilo'],
				]);
			});

			it('Object value logical AND', function () use ($json) {
				test_json_path($json, '$.o[?@>1 && @<4]', [2, 3]);
			});

			it('Object value logical OR', function () use ($json) {
				test_json_path($json, '$.o[?@.u || @.x]', [
					(object) ['u' => 6],
				]);
			});

			it('Comparison of queries with no values', function () use ($json) {
				test_json_path($json, '$.a[?@.b == $.x]', [3, 5, 1, 2, 4, 6]);
			});

			it(
				'Comparisons of primitive and of structured values',
				function () use ($json) {
					test_json_path($json, '$.a[?@ == @]', [
						3,
						5,
						1,
						2,
						4,
						6,
						(object) ['b' => 'j'],
						(object) ['b' => 'k'],
						(object) ['b' => (object) []],
						(object) ['b' => 'kilo'],
					]);
				},
			);

			// Tests added because they are necessary, although not exemplified in the RFC
			describe('Logical NOT', function () use ($json) {
				it('Non Existence value', function () use ($json) {
					test_json_path($json, '$.a[?!@.b]', [3, 5, 1, 2, 4, 6]);
				});

				it(
					'Non Existence value with enclosing parentheses',
					function () use ($json) {
						test_json_path($json, '$.a[?!(@.b)]', [
							3,
							5,
							1,
							2,
							4,
							6,
						]);
					},
				);

				it('Non Existence value with logical OR', function () use (
					$json
				) {
					test_json_path($json, '$.a[?!(@<2 || @.b == "k")]', [
						3,
						5,
						2,
						4,
						6,
						(object) ['b' => 'j'],
						(object) ['b' => (object) []],
						(object) ['b' => 'kilo'],
					]);
				});
			});

			describe('Function Extensions', function () use ($json) {
				describe('2.4.4. length() Function Extension', function () use (
					$json
				) {
					it('string length', function () use ($json) {
						test_json_path($json, '$.a[?length(@.b) >= 2]', [
							(object) ['b' => 'kilo'],
						]);
					});

					it('array length', function () {
						test_json_path(
							(object) [
								'a' => [1],
								'b' => [2, 3],
								'c' => [4, 5, 6],
							],
							'$[?length(@) > 2]',
							[[4, 5, 6]],
						);
					});

					it('object length', function () {
						test_json_path(
							(object) [
								'a' => (object) ['a' => 1],
								'b' => (object) ['a' => 1, 'b' => 2],
								'c' => (object) ['a' => 1, 'b' => 2, 'c' => 3],
							],
							'$[?length(@) == 2]',
							[(object) ['a' => 1, 'b' => 2]],
						);
					});

					it('select nothing from other argument type', function () {
						test_json_path(
							(object) ['a' => 1, 'b' => null, 'c' => true],
							'$[?length(@) == 2]',
							[],
						);
					});
				});
			});
		});
	});
});
