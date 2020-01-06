<?php

namespace kranack\Lint\Config;

use Exception;

use kranack\Lint\Exceptions\{ ConfigurationNotFound, ConfigurationNotValid };

class Config
{

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var object|null
	 */
	private $data;

	public function __construct(string $path)
	{
		$this->path = $path;
		$this->data = null;
	}

	public function validate() : bool
	{
		return $this->exists() && $this->isFormatValid();
	}

	public function exists() : bool
	{
		return file_exists($this->path);
	}

	public function isEmpty() : bool
	{
		return $this->data === null || empty($this->data);
	}

	public function isFormatValid() : bool
	{
		try {
			$this->read();
		} catch (Exception $e) {
			return false;
		}

		return !$this->isEmpty();
	}

	public function get(string $attr, $default = null)
	{
		return $this->data->{$attr} ?? $default;
	}

	public function open() : Config
	{
		$this->read();

		return $this;
	}

	private function read() : void
	{
		if (!$this->exists()) { throw new ConfigurationNotFound(); }

		try {
			$this->data = json_decode(file_get_contents($this->path));

			if ($this->isEmpty()) { throw new ConfigurationNotValid(); }
		} catch (Exception $e) {
			throw new ConfigurationNotValid($e);
		}
	}

	public static function init(string $path, ?object $config = null) : void
	{
		$parentPath = dirname($path);

		if (!file_exists($parentPath)) {
			mkdir($parentPath, 0777, true);
		}

		if ($config) {
			$config = (object) array_merge((array) static::getDefaultConfig(), (array) $config);
		} else {
			$config = static::getDefaultConfig();
		}

		file_put_contents($path, json_encode($config));
	}

	public static function getDefaultConfig() : object
	{
		return (object) [
			'paths'	=> [ ]
		];
	}

}