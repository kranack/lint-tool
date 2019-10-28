<?php

namespace kranack\Lint\Env\Scanner;

use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\IScanner;

class MacportsScanner implements IScanner
{

	const DEFAULT_HOME = '/opt/local';
	
	public function detect() : bool
	{
		if (OS::isWindows()) return false;

		return file_exists($this->getPrefix());
	}

	public function scan() : array
	{
		$prefix = $this->getPrefix();

		if (!file_exists($prefix) || !is_dir($prefix)) return [];

		$first = glob($prefix . '/php*/*/bin/php');
		$second = glob($prefix . '/bin/php??');

		return array_merge($first, $second);
	}

	public function isPathValid(string $path) : bool
	{
		$prefix = $this->getPrefix();

		if (stripos($path, $prefix) === false || !file_exists($path)) return false;
		
		return true;
	}

	public function extractVersion(string $path) : string
	{
		$version = basename(dirname(dirname($path)));
		$parts = explode('_', $version);

		// Ignore Macports custom versions (use _ separator)
		return reset($parts) ?? '1.0.0';
	}

	protected function getPrefix() : string
	{
		return static::DEFAULT_HOME;
	}

}