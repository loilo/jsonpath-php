<?php

namespace Loilo\JsonPath;

class PathSegmentsResult
{
	public function __construct(
		public mixed $value,
		/**
		 * @var string[]
		 */
		public array $segments
	) {}
}
