<?php

namespace Loilo\JsonPath;

class JsonPath
{
	private $root_node;

	public function __construct(private $query)
	{
		$parser = new PeggyParser();
		$parse_result = $parser->parse($query);
		$this->root_node = $parse_result;
	}

	public function find($json)
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(fn (Node $node) => $node->value, $result_node_list);
	}

	protected function convert_path_segment_to_string($segment)
	{
		if (is_string($segment)) {
			if ($segment === '$') {
				return $segment;
			}
			return "['{$segment}']";
		}
		return "[{$segment}]";
	}

	public function paths($json): array
	{
		$path_segments = $this->path_segments($json);
		return array_map(
			fn($result) => new PathResult(
				$result->value,
				'$' .
					join(
						'',
						array_map(
							fn($result) => $this->convert_path_segment_to_string($result),
							$result->segments,
						),
					),
			),
			$path_segments,
		);
	}

	public function path_segments($json): array
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(
			fn(Node $node) => new PathSegmentsResult($node->value, array_slice($node->path, 1)),
			$result_node_list,
		);
	}
}
