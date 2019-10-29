<?php

namespace kranack\Lint\Env\Scanner;

use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\IScanner;

class LocalScanner implements IScanner
{

	public function detect() : bool
	{
		if (OS::isWindows()) return false;

		return file_exists('/usr/bin/php') || file_exists('/usr/bin/local/php');
	}

	public function scan() : array
	{
		if (!file_exists('/usr/bin/php') || file_exists('/usr/bin/local/php')) return [];

		return array_merge(glob('/usr/bin/php'), glob('/usr/bin/local/php'), glob('/usr/bin/local/php*/php'));
	}

	public function isPathValid(string $path) : bool
	{
		return file_exists($path);
	}

	public function extractVersion(string $path) : string
	{
		return ScannerUtils::extractVersion($path);
	}

}