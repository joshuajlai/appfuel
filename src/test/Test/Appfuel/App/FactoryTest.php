<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Framework;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Framework\AppFactory;

/**
 * Test this class's static methods are able to create the correct objects
 */
class FactoryTest extends ParentTestCase
{
	/**
	 * @return NULL
	 */
	public function testCreatePhpError()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Init\PHPErrorInterface',
			AppFactory::createPHPError()
		);
	}

	/**
	 * @return NULL
	 */
	public function testCreateAutoloader()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Init\AutoloadInterface',
			AppFactory::createAutoloader()
		);
	}

	/**
	 * @return NULL
	 */
	public function testCreateIncludePath()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Init\IncludePath',
			AppFactory::createIncludePath()
		);
	}
}

