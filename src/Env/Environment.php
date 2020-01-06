<?php

namespace kranack\Lint\Env;

use kranack\Lint\Config\Config;
use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\{ HomebrewScanner, IScanner, LocalScanner, MacportsScanner };

class Environment
{

	private object $data;

	public function __construct(string $version = '0.0.0')
	{
		$this->data = (object) [ 'version' => $version ];
	}

	public function init() : void
	{
		$this->scan();

		Config::init(static::getConfigFilePath(), $this->data);
	}

	public function scan() : void
	{
		$this->scanHomebrewInstall();
		$this->scanMacPortsInstall();
		$this->scanLocalInstall();
	}

	private function scanHomebrewInstall() : void
	{
		$this->appendToPaths($this->scanInstall('Homebrew'));
	}

	private function scanMacPortsInstall() : void
	{
		$this->appendToPaths($this->scanInstall('Macports'));
	}

	private function scanLocalInstall() : void
	{
		$this->appendToPaths($this->scanInstall('Local'));
	}

	private function scanInstall(string $type) : array
	{
		$scanner = $this->getScanner($type);
		
		if (!$scanner->detect()) return [];
		
		return array_map(fn($path) => (object) [ 'path' => $path, 'type' => $type, 'version' => $scanner->extractVersion($path) ], $scanner->scan());
	}

	private function getScanner(string $type) : IScanner
	{
		switch ($type) {
			case 'Homebrew':
				return new HomebrewScanner();
			case 'Macports':
				return new MacportsScanner();
			case 'Local':
			default:
				return new LocalScanner();
		}
	}

	private function appendToPaths(array $paths)
	{
		$this->data->paths = array_merge($this->data->paths ?? [] , $paths);
	}

	public static function getRootPath() : string
	{
		return LINT_ROOT_FOLDER . DIRECTORY_SEPARATOR;
	}

	public static function getConfigFilePath(?string $root = null) : string
	{
		return ($root ?? static::getRootPath()) . sprintf('conf%sconfig.json', DIRECTORY_SEPARATOR);
	}

	public static function getConfig(?string $root = null) : ?Config
	{
		$configPath = static::getConfigFilePath($root);

		if (!file_exists($configPath)) {
			return null;
		}

		return (new Config($configPath))->open();
	}

	public static function isConfigured()
	{
		$config = static::getConfig();

		return $config ? $config->validate() : false;
	}

	public static function extractVersion(object $binary)
	{
		$scanner = (new Environment)->getScanner($binary->type ?? 'Local');
		
		if (!$scanner->isPathValid($binary->path)) return '0.0.0';

		return $scanner->extractVersion($binary->path);
	}

}