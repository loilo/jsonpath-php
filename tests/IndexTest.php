<?php

declare(strict_types=1);

require_once 'utils.php';

$book1 = (object) [
	'category' => 'reference',
	'author' => 'Nigel Rees',
	'title' => 'Sayings of the Century',
	'price' => 8.95,
];

$book2 = (object) [
	'category' => 'fiction',
	'author' => 'Evelyn Waugh',
	'title' => 'Sword of Honour',
	'price' => 12.99,
];

$book3 = (object) [
	'category' => 'fiction',
	'author' => 'Herman Melville',
	'title' => 'Moby Dick',
	'isbn' => '0-553-21311-3',
	'price' => 8.99,
];

$book4 = (object) [
	'category' => 'fiction',
	'author' => 'J. R. R. Tolkien',
	'title' => 'The Lord of the Rings',
	'isbn' => '0-395-19395-8',
	'price' => 22.99,
];

$json = (object) [
	'store' => (object) [
		'book' => [$book1, $book2, $book3, $book4],
		'bicycle' => (object) [
			'color' => 'red',
			'price' => 19.95,
		],
	],
];

// https://goessner.net/articles/JsonPath/index.html#e2
describe('Stefan Goessner JsonPath implementation', function () use (
	$json,
	$book1,
	$book2,
	$book3,
	$book4
) {
	it('child operator', function () use ($json) {
		test_json_path($json, '$.store.bicycle.color', ['red']);
	});

	it('recursive descent', function () use (
		$json,
		$book1,
		$book2,
		$book3,
		$book4
	) {
		test_json_path($json, '$..author', [
			$book1->author,
			$book2->author,
			$book3->author,
			$book4->author,
		]);
	});

	it('wildcard', function () use ($json, $book1, $book2, $book3, $book4) {
		test_json_path($json, '$.store.book.*', [
			$book1,
			$book2,
			$book3,
			$book4,
		]);
	});

	it('subscript operator', function () use ($json, $book3) {
		test_json_path($json, '$..book[2]', [$book3]);
	});

	it('array slice operator borrowed from ES4', function () use (
		$json,
		$book2,
		$book3
	) {
		test_json_path($json, '$..book[1:3]', [$book2, $book3]);
	});

	it('array slice operator with slice', function () use (
		$json,
		$book1,
		$book3
	) {
		test_json_path($json, '$..book[0:3:2]', [$book1, $book3]);
	});

	it('array slice operator with end', function () use (
		$json,
		$book1,
		$book2
	) {
		test_json_path($json, '$..book[:2]', [$book1, $book2]);
	});

	it('applies a filter expression', function () use ($json, $book3, $book4) {
		test_json_path($json, '$..book[?(@.isbn)]', [$book3, $book4]);
	});

	it('applies a script expression', function () use ($json, $book1, $book3) {
		test_json_path($json, '$..book[?(@.price<10)]', [$book1, $book3]);
	});
});
