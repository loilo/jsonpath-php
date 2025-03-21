<?php

namespace Loilo\JsonPath;

interface FunctionTypeDef
{
	public function get_type(): string;
	public function convert($arg);
}

class ValueTypeDef implements FunctionTypeDef
{
	private $type = 'ValueType';

	public function get_type(): string
	{
		return $this->type;
	}

	public function convert($arg)
	{
		if ($arg === nothing()) {
			return $arg;
		}
		if (is_json_primitive($arg)) {
			return $arg;
		}
		if (is_node($arg)) {
			return $arg->value;
		}
		if (is_node_list($arg)) {
			if (sizeof($arg) === 0) {
				return nothing();
			}
			if (sizeof($arg) === 1) {
				return $arg[0]->value;
			}
		}

		throw new \Exception(
			'Invalid argument type "' . json_encode($arg) . '" is not a ValueType',
		);
	}
}

class NodesTypeDef implements FunctionTypeDef
{
	private $type = 'NodesType';

	public function get_type(): string
	{
		return $this->type;
	}

	public function convert($arg)
	{
		if (is_node_list($arg)) {
			return $arg;
		}

		throw new \Exception(
			'Invalid argument type "' . json_encode($arg) . '" is not a NodesType',
		);
	}
}

class LogicalTypeDef implements FunctionTypeDef
{
	private $type = 'LogicalType';

	public function get_type(): string
	{
		return $this->type;
	}

	public function convert($arg)
	{
		if ($arg === true) {
			return LogicalType::true();
		}

		if ($arg === false) {
			return LogicalType::false();
		}

		if (is_json_array($arg)) {
			if (sizeof($arg) === 0) {
				return LogicalType::false();
			}
			if (sizeof($arg) >= 1) {
				return LogicalType::true();
			}
		}

		throw new \Exception(
			'Invalid argument type "' . json_encode($arg) . '" is not a LogicalType',
		);
	}
}

class FunctionDefinition
{
	public $name;
	public $args;
	public $return;
	private $function;

	public function __construct($name, $args, $return, $function)
	{
		$this->name = $name;
		$this->args = $args;
		$this->return = $return;
		$this->function = $function;
	}

	function function()
	{
		return call_user_func_array($this->function, func_get_args());
	}
}

function extract_args($function_definition, $args)
{
	$arg_defs = $function_definition->args;
	if (sizeof($args) !== sizeof($arg_defs)) {
		throw new \Exception(
			'Invalid number of arguments: ' .
				$function_definition->name .
				' function requires ' .
				sizeof($arg_defs) .
				' arguments but received ' .
				sizeof($args),
		);
	}

	$converted_args = array_map(
		function ($def, $index) use ($args) {
			return $def->convert($args[$index]);
		},
		$arg_defs,
		array_keys($arg_defs),
	);

	return $converted_args;
}
