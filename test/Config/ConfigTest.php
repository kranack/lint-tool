<?php

namespace kranack\Lint\Test;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\{ vfsStream, vfsStreamDirectory };

use kranack\Lint\Config\Config;
use kranack\Lint\Exceptions\{ ConfigurationNotFound, ConfigurationNotValid };

class ConfigTest extends TestCase
{

	private $dir;

	public function setUp() : void
	{
		$this->dir = vfsStream::setup('conf');
		$this->dir->addChild(vfsStream::newFile('config.json'));
		$this->dir->addChild(vfsStream::newFile('config-not-valid.json'));

		file_put_contents(vfsStream::url('conf/config.json'), '{"paths":[]}');
	}

	public function testFileExists()
	{
		$config = new Config(vfsStream::url('conf/config.json'));

		$this->assertTrue($config->exists());
	}

	public function testFileNotEmpty()
	{
		$config = new Config(vfsStream::url('conf/config.json'));
		$config->open();

		$this->assertFalse($config->isEmpty());
	}

	public function testFileNotValid()
	{
		$this->expectException(ConfigurationNotValid::class);

		$config = new Config(vfsStream::url('conf/config-not-valid.json'));
		$config->open();
	}

	public function testFileNotFound()
	{
		$this->expectException(ConfigurationNotFound::class);

		$config = new Config(vfsStream::url('conf/config-not-exists.json'));
		$config->open();
	}

	public function testFileHasPathAttribute()
	{
		$config = new Config(vfsStream::url('conf/config.json'));
		$config->open();

		$paths = $config->get('paths');

		$this->assertNotNull($paths);
		$this->assertIsArray($paths);
	}

}