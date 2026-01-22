<?php

namespace Loilo\JsonPath;

class PathResult
{
	public function __construct(public mixed $value, public string $path) {}
}
