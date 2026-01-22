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

	public function paths($json)
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(
			fn (Node $node) => [
				'value' => $node->value,
				'path' => implode(array_map(fn ($segment) => $this->convert_path_segment_to_string($segment), $node->path)),
			],
			$result_node_list,
		);
	}

	public function pathSegments($json)
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(fn (Node $node) => [
			'value' => $node->value,
			'segments' => array_slice($node->path, 1)
		], $result_node_list);
	}
}
