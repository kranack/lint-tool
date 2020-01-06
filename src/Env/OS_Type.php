<?php

namespace kranack\Lint\Env;

abstract class OS_Type
{
	const UNKNOWN = 0;
	const WIN = 1;
	const OSX = 2;
	const LINUX = 3;
	const UNIX = 4;
}