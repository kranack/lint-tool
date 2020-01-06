<?php

namespace kranack\Lint\Test\Env;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\{ vfsStream, vfsStreamDirectory };

use kranack\Lint\Config\Config;
use kranack\Lint\Env\Environment;

class EnvironmentTest extends TestCase
{
	
	use MockeryPHPUnitIntegration;

	private $root;

	public function setUp() : void
	{
		$this->root = vfsStream::setup('root');
		$this->root->addChild(vfsStream::newDirectory('conf'));
		$this->root->addChild(vfsStream::newFile('conf/config.json'));

		file_put_contents(vfsStream::url('root/conf/config.json'), '{"paths":[], "version": "1.0.0"}');
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	/**
	 * @covers kranack\Lint\Env\Environment::getConfigFilePath
	 */
	public function testGetConfigFilePath() : void
	{
		$path = Environment::getConfigFilePath($this->root->url() . '/');

		$this->assertEquals(vfsStream::url('root/conf/config.json'), $path);
	}

	/**
	 * @covers kranack\Lint\Env\Environment::getConfig
	 * @covers kranack\Lint\Env\Environment::getConfigFilePath
	 */
	public function testGetConfigIsNull() : void
	{
		$config = Environment::getConfig(vfsStream::url('root/conf') . '/');

		$this->assertNull($config);
	}

	/**
	 * @covers kranack\Lint\Env\Environment::getConfig
	 * @covers kranack\Lint\Env\Environment::getConfigFilePath
	 * 
	 * @uses kranack\Lint\Config\Config
	 */
	public function testGetConfig() : void
	{
		$config = Environment::getConfig($this->root->url() . '/');

		$this->assertNotNull($config);
		$this->assertInstanceOf(Config::class, $config);
		$this->assertIsArray($config->get('paths'));
		$this->assertIsString($config->get('version'));

		$this->assertEquals([], $config->get('paths'));
		$this->assertEquals('1.0.0', $config->get('version'));
	}

	/**
	 * @covers kranack\Lint\Env\Environment::__construct
	 * @covers kranack\Lint\Env\Environment::init
	 * @covers kranack\Lint\Env\Environment::getConfig
	 * @covers kranack\Lint\Env\Environment::getConfigFilePath
	 * 
	 * @uses kranack\Lint\Config\Config
	 */
	public function testScan() : void
	{
		$env = Mockery::mock(Environment::class, [ '2.0.0' ]);
		$env->shouldReceive('init')
			->once();
		
		$env->allows([
			'getRootPath'	=> $this->root->url() . '/'
		]);

		$env->init();

		$config = Environment::getConfig($this->root->url() . '/');

		$this->assertIsArray($config->get('paths'));
		$this->assertContainsOnly('object', $config->get('paths'));

		$this->assertEquals('1.0.0', $config->get('version'));
	}

}