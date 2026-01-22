<?php

declare(strict_types=1);

use Loilo\JsonPath\Node;

use function Loilo\JsonPath\create_node;
use function Loilo\JsonPath\traverse_descendant;

describe('traverseDescendant', function () {
	test('empty object traverses empty', function () {
		$node = create_node((object) [], []);
		expect(
			array_map(
				fn (Node $node) => $node->value,
				traverse_descendant($node),
			),
		)->toEqual([(object) []]);
	});

	test('nested arrays traverse correctly', function () {
		$node = create_node([[[1]], [2]], []);
		expect(
			array_map(
				fn (Node $node) => $node->value,
				traverse_descendant($node),
			),
		)->toEqual([[[[1]], [2]], [[1]], [1], 1, [2], 2]);
	});
});
