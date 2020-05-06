<?php

namespace kranack\Lint\Env;

use stdClass;

use kranack\Lint\Config\Config;
use kranack\Lint\Env\OS;
use kranack\Lint\Env\Scanner\{ HomebrewScanner, IScanner, LocalScanner, MacportsScanner, Scanner_Type };

class Environment
{

	private $data;

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
		$this->appendToPaths($this->scanInstall(Scanner_Type::HOMEBREW));
	}

	private function scanMacPortsInstall() : void
	{
		$this->appendToPaths($this->scanInstall(Scanner_Type::MACPORTS));
	}

	private function scanLocalInstall() : void
	{
		$this->appendToPaths($this->scanInstall(Scanner_Type::LOCAL));
	}

	private function scanInstall(string $type) : array
	{
		$scanner = $this->getScanner($type);
		
		if (!$scanner->detect()) return [];
		
		$versions = array_map(function(string $path) use ($type, $scanner) {
			return (object) [ 'path' => $path, 'type' => $type, 'version' => $scanner->extractVersion($path) ];
		}, $scanner->scan());

		// Filter versions
		$versions = array_filter($versions, function($version) {
			$code = 0;

			ob_start();
			passthru(sprintf('%s -v 2>&1', $version->path), $code);
			ob_end_clean();

			return $code === 0;
		});

		return $versions;
	}

	private function getScanner(string $type) : IScanner
	{
		switch ($type) {
			case Scanner_Type::HOMEBREW:
				return new HomebrewScanner();
			case Scanner_Type::MACPORTS:
				return new MacportsScanner();
			case Scanner_Type::LOCAL:
			default:
				return new LocalScanner();
		}
	}

	private function appendToPaths(array $paths) : void
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

	public static function isConfigured() : bool
	{
		$config = static::getConfig();

		return $config ? $config->validate() : false;
	}

	public static function extractVersion(stdClass $binary) : string
	{
		$scanner = (new Environment)->getScanner($binary->type ?? Scanner_Type::LOCAL);
		
		if (!$scanner->isPathValid($binary->path)) return '0.0.0';

		return $scanner->extractVersion($binary->path);
	}

}