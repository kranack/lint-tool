<?php

namespace kranack\Lint\Env;

use stdClass;

use kranack\Lint\Config\Config;
use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\{ HomebrewScanner, IScanner, LocalScanner, MacportsScanner };

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
		
		return array_map(function(string $path) use ($type, $scanner) {
			return (object) [ 'path' => $path, 'type' => $type, 'version' => $scanner->extractVersion($path) ];
		}, $scanner->scan());
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
		return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
	}

	public static function getConfigFilePath(?string $root = null) : string
	{
		return ($root ?? static::getRootPath()) . sprintf('conf%sconfig.json', DIRECTORY_SEPARATOR);
	}

	public static function getConfig() : ?Config
	{
		$configPath = static::getConfigFilePath();

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

	public static function extractVersion(stdClass $binary)
	{
		$scanner = (new static)->getScanner($binary->type ?? 'Local');
		
		if (!$scanner->isPathValid($binary->path)) return '0.0.0';

		return $scanner->extractVersion($binary->path);
	}

}