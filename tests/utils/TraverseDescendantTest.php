<?php

declare(strict_types=1);

use function Loilo\JsonPath\traverse_descendant;

describe('traverseDescendant', function () {
	test('empty object traverses empty', function () {
		$json = (object) [];
		expect(traverse_descendant($json))->toEqual([(object) []]);
	});

	test('nested arrays traverse correctly', function () {
		$json = [[[1]], [2]];
		expect(traverse_descendant($json))->toEqual([
			[[[1]], [2]],
			[[1]],
			[1],
			1,
			[2],
			2,
		]);
	});
});
