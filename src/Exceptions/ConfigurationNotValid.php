<?php

namespace kranack\Lint\Exceptions;

use Exception;

class ConfigurationNotValid extends Exception
{

	public function __construct(?Exception $previous = null)
	{
		parent::__construct('Configuration file not valid', 3, $previous);
	}

}