<?php

namespace kranack\Lint\Env;

abstract class OS_TYPE
{
	const UNKNOWN = 0;
	const WIN = 1;
	const OSX = 2;
	const LINUX = 3;
	const UNIX = 4;
}

class OS
{

	public static function isWindows() : bool
	{
		return stripos(PHP_OS, 'win') === 0;
	}

	public static function isOSX() : bool
	{
		return stripos(PHP_OS, 'darwin') === 0;
	}

	public static function isLinux() : bool
	{
		return stripos(PHP_OS, 'linux') === 0;
	}

	public static function isUnix() : bool
	{
		return stripos(PHP_OS, 'unix') === 0;
	}

	public static function detect() : int
	{
		if (static::isWindows()) return OS_TYPE::WIN;
		if (static::isOSX()) return OS_TYPE::OSX;
		if (static::isLinux()) return OS_TYPE::LINUX;
		if (static::isUnix()) return OS_TYPE::UNIX;

		return OS_TYPE::UNKNOWN;
	}

}