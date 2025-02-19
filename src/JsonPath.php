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
		return $result_node_list;
	}
}
