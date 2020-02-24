<?php

namespace kranack\Lint\Test\Config;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\{ vfsStream, vfsStreamDirectory };

use kranack\Lint\Config\Config;
use kranack\Lint\Exceptions\{ ConfigurationNotFound, ConfigurationNotValid };

class ConfigTest extends TestCase
{

	/**
	 * @var vfsStreamDirectory
	 */
	private $dir;

	public function setUp() : void
	{
		$this->dir = vfsStream::setup('conf');
		$this->dir->addChild(vfsStream::newFile('config.json'));
		$this->dir->addChild(vfsStream::newFile('config-not-valid.json'));

		file_put_contents(vfsStream::url('conf/config.json'), '{"paths":[]}');
	}

	/**
	 * @covers kranack\Lint\Config\Config
	 */
	public function testFileExists() : void
	{
		$config = new Config(vfsStream::url('conf/config.json'));

		$this->assertTrue($config->exists());
	}

	/**
	 * @covers kranack\Lint\Config\Config
	 */
	public function testFileNotEmpty() : void
	{
		$config = new Config(vfsStream::url('conf/config.json'));
		$config->open();

		$this->assertFalse($config->isEmpty());
	}

	/**
	 * @covers kranack\Lint\Config\Config
	 * 
	 * @uses kranack\Lint\Exceptions\ConfigurationNotValid
	 */
	public function testFileNotValid() : void
	{
		$this->expectException(ConfigurationNotValid::class);

		$config = new Config(vfsStream::url('conf/config-not-valid.json'));
		$config->open();
	}

	/**
	 * @covers kranack\Lint\Config\Config
	 * 
	 * @uses kranack\Lint\Exceptions\ConfigurationNotFound
	 */
	public function testFileNotFound() : void
	{
		$this->expectException(ConfigurationNotFound::class);

		$config = new Config(vfsStream::url('conf/config-not-exists.json'));
		$config->open();
	}

	/**
	 * @covers kranack\Lint\Config\Config
	 */
	public function testFileHasPathAttribute() : void
	{
		$config = new Config(vfsStream::url('conf/config.json'));
		$config->open();

		$paths = $config->get('paths');

		$this->assertNotNull($paths);
		$this->assertIsArray($paths);
	}

}