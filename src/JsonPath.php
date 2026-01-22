<?php

namespace Loilo\JsonPath;

/**
 * A JSONPath query engine for executing JSONPath queries against JSON data.
 * Fully implements the RFC 9535 JSONPath specification.
 */
class JsonPath
{
	private $root_node;

	/**
	 * Creates a new JSONPath query instance.
	 * @param string $query The JSONPath query string to parse
	 * @throws \Exception Throws an error if the query string is invalid
	 */
	public function __construct(private string $query)
	{
		$parser = new PeggyParser();
		$parse_result = $parser->parse($query);
		$this->root_node = $parse_result;
	}

	/**
	 * Executes the JSONPath query and returns only the matching values.
	 * @param mixed $json The JSON data to query against
	 * @return array An array of matching values
	 */
	public function find($json): array
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(fn(Node $node) => $node->value, $result_node_list);
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

	/**
	 * Executes the JSONPath query and returns both matching values and their JSONPath strings.
	 * @param mixed $json The JSON data to query against
	 * @return array An array of objects containing the matching value and its JSONPath string
	 */
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

	/**
	 * Executes the JSONPath query and returns both matching values and their path segments as arrays.
	 * Path segments are returned as arrays containing strings (for object keys) and numbers (for array indices).
	 * The root segment $ is not included in path segments.
	 * @param mixed $json The JSON data to query against
	 * @return PathSegmentsResult[] An array of objects containing the matching value and its path segments as an array
	 */
	public function path_segments($json): array
	{
		$result_node_list = run($json, $this->root_node);
		return array_map(
			fn(Node $node) => new PathSegmentsResult($node->value, array_slice($node->path, 1)),
			$result_node_list,
		);
	}
}
