<?php

declare(strict_types=1);

namespace Projom\Http\Route;

enum ParameterType: string
{
	case NUMERIC_ID = 'numeric_id';
	case ID = 'id';
	case INTEGER = 'integer';
	case INT = 'int';
	case STRING = 'string';
	case STR = 'str';
	case BOOL = 'bool';
	case NAME = 'name';

	public function toSubstitute(): string
	{
		$substitute = '/' . '{' . $this->value . '}' . '/';
		return $substitute;
	}

	public function toPattern(): string
	{
		$pattern = match ($this) {
			ParameterType::NUMERIC_ID, ParameterType::ID => '([1-9][0-9]*)',
			ParameterType::INTEGER, ParameterType::INT => '(\-?[0-9]+)',
			ParameterType::STRING, ParameterType::STR => '(.+)',
			ParameterType::BOOL => '(true|false)',
			ParameterType::NAME => '([0-9a-zA-Z_@,.\-\s]+)'
		};

		$pattern = "/^$pattern$/";
		return $pattern;
	}
}
