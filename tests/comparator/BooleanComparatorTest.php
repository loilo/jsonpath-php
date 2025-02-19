<?php

declare(strict_types=1);

use function Loilo\JsonPath\compare_booleans;

describe('BooleanComparator', function () {
	it('eq returns true for equal true booleans', function () {
		expect(compare_booleans('==', true, true))->toBe(true);
	});

	it('eq returns true for equal false booleans', function () {
		expect(compare_booleans('==', false, false))->toBe(true);
	});

	it('eq returns false for different booleans', function () {
		expect(compare_booleans('==', true, false))->toBe(false);
	});

	it('Booleans do not offer lt comparison', function () {
		expect(compare_booleans('<', true, true))->toBe(false);
		expect(compare_booleans('<=', true, true))->toBe(true);
		expect(compare_booleans('>', true, true))->toBe(false);
		expect(compare_booleans('>=', true, true))->toBe(true);
	});
});
