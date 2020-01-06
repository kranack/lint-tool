<?php

namespace kranack\Lint\Env;

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
		if (static::isWindows()) return OS_Type::WIN;
		if (static::isOSX()) return OS_Type::OSX;
		if (static::isLinux()) return OS_Type::LINUX;
		if (static::isUnix()) return OS_Type::UNIX;

		return OS_Type::UNKNOWN;
	}

}