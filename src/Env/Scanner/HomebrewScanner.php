<?php

namespace kranack\Lint\Env\Scanner;

use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\IScanner;

class HomebrewScanner implements IScanner
{

	const PREFIX = 'HOMEBREW_PREFIX';
	const DEFAULT_HOME = '/usr/local';
	
	public function detect() : bool
	{
		if (OS::isWindows()) return false;

		[ $status ] = $this->getConfig();

		return $status === 0;
	}

	public function scan() : array
	{
		$prefix = $this->getPrefix();

		if (!file_exists($prefix . '/Cellar') || !is_dir($prefix . '/Cellar')) return [];

		return glob($prefix . '/Cellar/php*/*/bin/php');
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

		// Ignore Homebrew custom versions (use _ separator)
		return reset($parts) ?? '1.0.0';
	}

	protected function getPrefix() : string
	{
		[ , $config ] = $this->getConfig();

		$first = strstr($config, static::PREFIX);

		if ($first === false) return static::DEFAULT_HOME;

		$home = explode("\n", $first);

		if (count($home)) {
			$home = explode(':', $home[0]);
			$home[0] = $home[1] ?? null;
		}
		
		$home = $home[0] ?? static::DEFAULT_HOME;

		return trim($home);
	}

	protected function getConfig() : array
	{
		ob_start();
		passthru('brew config', $status);
		$config = ob_get_clean();

		return [ $status, $config ];
	}

}