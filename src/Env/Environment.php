<?php

namespace kranack\Lint\Env;

use kranack\Lint\Config\Config;

class Environment
{

	private $data;

	public function __construct()
	{
		$this->data = (object) [ ];
	}

	public function init() : void
	{
		$this->scan();

		Config::init(static::getConfigFilePath(), $this->data);
	}

	public function scan() : void
	{
		$this->scanHomebrewInstall();
	}

	private function scanHomebrewInstall() : void
	{
		if ((!stristr(PHP_OS, 'dar') && !stristr(PHP_OS, 'linux')) || !file_exists('/usr/local') || !is_dir('/usr/local')) return ;

		$this->data->php = glob('/usr/local/Cellar/php*/*/bin/php');
	}

	public static function getRootPath() : string
	{
		return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
	}

	public static function getConfigFilePath(?string $root = null) : string
	{
		return ($root ?? static::getRootPath()) . 'conf' . DIRECTORY_SEPARATOR . 'config.json';
	}

	public static function getConfig() : ?Config
	{
		$configPath = static::getConfigFilePath();

		if (!file_exists($configPath)) {
			return null;
		}

		return new Config($configPath);
	}

	public static function isConfigured()
	{
		$config = static::getConfig();

		return $config ? $config->validate() : false;
	}

	public static function extractVersion(string $path)
	{
		// is Homebrew
		$isHomebrew = (strpos($path, '/usr/local/Cellar') === 0);

		if (!$isHomebrew) return '0.0.0';

		$version = basename(dirname(dirname($path)));
		$parts = explode('_', $version);

		// Ignore Homebrew custom versions (use _ separator)
		return reset($parts) ?? '1.0.0';
	}

}