<?php

declare(strict_types=1);

require_once __DIR__ . '/../utils.php';

use Loilo\JsonPath\JsonPath;

// https://github.com/jsonpath-standard/jsonpath-compliance-test-suite
describe('JSONPath Compliance Test Suite', function () {
	$cts = json_decode(
		file_get_contents(__DIR__ . '/jsonpath-compliance-test-suite/cts.json'),
	);

	$skip_tests = [
		'name selector, double quotes, escaped backspace' =>
			'PHP cannot properly decode "\b" as a key, test is manually added',
		'name selector, single quotes, escaped backspace' =>
			'PHP cannot properly decode "\b" as a key, test is manually added',
		'name selector, single quotes, surrogate pair 𝄞' =>
			'Skip UTF-16 related tests',
		'name selector, double quotes, surrogate pair 𝄞' =>
			'Skip UTF-16 related tests',
		'name selector, single quotes, surrogate pair 😀' =>
			'Skip UTF-16 related tests',
		'name selector, double quotes, surrogate pair 😀' =>
			'Skip UTF-16 related tests',
		'name selector, double quotes, supplementary surrogate' =>
			'Skip UTF-16 related tests',
		'name selector, double quotes, surrogate supplementary' =>
			'Skip UTF-16 related tests',
		'name selector, double quotes, supplementary plane character' =>
			'Skip UTF-16 related tests',
	];

	foreach ($cts->tests as $index => $test_case) {
		$pest_cases = [];
		if ($test_case->invalid_selector ?? false) {
			$pest_cases[] = test(
				"{$index}: {$test_case->name}",
				function () use ($test_case) {
					expect(function () use ($test_case) {
						try {
							(new JsonPath($test_case->selector))->find(
								(object) [
									'a' => 'b',
									'c' => (object) [
										'd' => ['e', 'f'],
										'g' => 'h',
									],
								],
							);
						} catch (Throwable $e) {
							throw new Exception();
						}
					})->toThrow(Exception::class);
				},
			);
		} else {
			$pest_cases[] = test("{$index}: $test_case->name", function () use (
				$test_case
			) {
				test_json_path(
					json: $test_case->document,
					jsonpath: $test_case->selector,
					expected: $test_case->result ?? $test_case->results[0],
				);
			});

			if ($test_case->result_paths ?? false) {
				$pest_cases[] = test(
					"{$index}: {$test_case->name} Normalized Path",
					function () use ($test_case) {
						test_normalized_path(
							json: $test_case->document,
							jsonpath: $test_case->selector,
							expected: $test_case->result_paths,
						);
					},
				);
			}
		}

		if (isset($skip_tests[$test_case->name])) {
			foreach ($pest_cases as $pest_case) {
				$pest_case->skip($skip_tests[$test_case->name]);
			}
		}
	}
});

describe('Manually extracted from JSONPath Compliance Test Suite', function () {
	$tests = [
		<<<'JSON'
		{
			"name": "name selector, single quotes, escaped backspace",
			"selector": "$['\\b']",
			"document": {
				"\\b": "A"
			},
			"result": [
				"A"
			],
			"result_paths": [
				"$['\\b']"
			]
		}
		JSON
		,
		<<<'JSON'
		{
			"name": "name selector, double quotes, escaped backspace",
			"selector": "$[\"\\b\"]",
			"document": {
				"\\b": "A"
			},
			"result": [
				"A"
			],
			"result_paths": [
				"$['\\b']"
			]
		}
		JSON
	,
	];

	foreach ($tests as $index => $test_case_json) {
		$test_case = json_decode($test_case_json);

		if ($test_case->invalid_selector ?? false) {
			test("{$index}: {$test_case->name}", function () use ($test_case) {
				expect(
					fn() => (new JsonPath($test_case->selector))->find(
						(object) [
							'a' => 'b',
							'c' => (object) ['d' => ['e', 'f'], 'g' => 'h'],
						],
					),
				)->toThrow(Exception::class);
			});
		} else {
			test("{$index}: $test_case->name", function () use ($test_case) {
				test_json_path(
					$test_case->document,
					$test_case->selector,
					$test_case->result ?? $test_case->results[0],
				);
			});
		}
	}
});
