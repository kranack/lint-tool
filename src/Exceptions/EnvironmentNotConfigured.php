<?php

namespace kranack\Lint\Exceptions;

use Exception;

class EnvironmentNotConfigured extends Exception
{

	public function __construct()
	{
		parent::__construct('No configuration found, please run install command first', 2);
	}

}