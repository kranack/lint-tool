<?php

namespace kranack\Lint\Test\Env;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

use kranack\Lint\Env\{ OS, OS_Type };

class OSTest extends TestCase
{
	
	use MockeryPHPUnitIntegration;

	public function setUp() : void
	{
	}

	public function tearDown() : void
	{
		Mockery::close();
	}

	/**
	 * @requires OS Linux
	 * @return void
	 */
	public function testDetectLinux() : void
	{
		$this->assertTrue(OS::isLinux());
		$this->assertEquals(OS_Type::LINUX, OS::detect());
	}

	/**
	 * @requires OS Unix
	 * @return void
	 */
	public function testDetectUnix() : void
	{
		$this->assertTrue(OS::isUnix());
		$this->assertEquals(OS_Type::UNIX, OS::detect());
	}

	/**
	 * @requires OSFAMILY Windows
	 * @return void
	 */
	public function testDetectWindows() : void
	{
		$this->assertTrue(OS::isWindows());
		$this->assertEquals(OS_Type::WIN, OS::detect());
	}

	/**
	 * @requires OS Darwin
	 * @return void
	 */
	public function testDetectOSX() : void
	{
		$this->assertTrue(OS::isOSX());
		$this->assertEquals(OS_Type::OSX, OS::detect());
	}

}