<?php

namespace Loilo\JsonPath;

// 2.3.4. Array Slice Selector
// The array slice selector has the form <start>:<end>:<step>.
// It matches elements from arrays starting at index < start >
// and ending at(but not including)<end>,
// while incrementing by step with a default of 1.
function apply_slice_selector($selector, $json)
{
	if (!is_json_array($json)) {
		return [];
	}

	$step = $selector->step ?? 1;
	$start = $selector->start ?? ($step >= 0 ? 0 : sizeof($json) - 1);
	$end = $selector->end ?? ($step >= 0 ? sizeof($json) : -sizeof($json) - 1);
	$array = [];

	[$lower, $upper] = bounds($start, $end, $step, sizeof($json));

	// IF step > 0 THEN
	//
	//   i = lower
	//   WHILE i < upper:
	//     SELECT a(i)
	//     i = i + step
	//   END WHILE
	//
	// ELSE if step < 0 THEN
	//
	//   i = upper
	//   WHILE lower < i:
	//     SELECT a(i)
	//     i = i + step
	//   END WHILE
	//
	// END IF
	if ($step > 0) {
		for ($i = $lower; $i < $upper; $i += $step) {
			$array[] = $json[$i];
		}
	} elseif ($step < 0) {
		for ($i = $upper; $lower < $i; $i += $step) {
			$array[] = $json[$i];
		}
	}

	return $array;
}

// FUNCTION Normalize(i, len):
//   IF i >= 0 THEN
//     RETURN i
//   ELSE
//     RETURN len + i
//   END IF
function normalized($index, $length)
{
	if ($index >= 0) {
		return $index;
	}
	return $length + $index;
}

// FUNCTION Bounds(start, end, step, len):
//   n_start = Normalize(start, len)
//   n_end = Normalize(end, len)
//
//   IF step >= 0 THEN
//     lower = MIN(MAX(n_start, 0), len)
//     upper = MIN(MAX(n_end, 0), len)
//   ELSE
//     upper = MIN(MAX(n_start, -1), len-1)
//     lower = MIN(MAX(n_end, -1), len-1)
//   END IF
//
//   RETURN (lower, upper)
function bounds($start, $end, $step, $length)
{
	$n_start = normalized($start, $length);
	$n_end = normalized($end, $length);

	if ($step >= 0) {
		$lower = min(max($n_start, 0), $length);
		$upper = min(max($n_end, 0), $length);
	} else {
		$upper = min(max($n_start, -1), $length - 1);
		$lower = min(max($n_end, -1), $length - 1);
	}

	return [$lower, $upper];
}
