<?php

declare(strict_types=1);

use Loilo\JsonPath\JsonPath;

$book_1 = [
	'category' => 'reference',
	'author' => 'Nigel Rees',
	'title' => 'Sayings of the Century',
	'price' => 8.95,
];

$book_2 = [
	'category' => 'fiction',
	'author' => 'Evelyn Waugh',
	'title' => 'Sword of Honour',
	'price' => 12.99,
];

$json = [
	'store' => [
		'book' => [$book_1, $book_2],
	],
];

it(
	'should return path segments as arrays of strings and numbers',
	function () use ($json) {
		$path = new JsonPath("$.store.book[*].author");
		$path_segments_list = array_map(
			fn($result) => $result->segments,
			$path->path_segments($json),
		);

		expect($path_segments_list[0])->toEqual(['store', 'book', 0, 'author']);
		expect($path_segments_list[1])->toEqual(['store', 'book', 1, 'author']);
	},
);

it('should return empty segments for root segment', function () use ($json) {
	$path = new JsonPath("$");
	$path_segments_list = array_map(
		fn($result) => $result->segments,
		$path->path_segments($json),
	);

	expect($path_segments_list[0])->toEqual([]);
});
