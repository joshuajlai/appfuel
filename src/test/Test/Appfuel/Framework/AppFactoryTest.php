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
 * 
 */
class FactoryTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Framework\App\Factory
	 */
	protected $factory = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->basePath = $this->getBasePath();
		$this->factory  = new AppFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->factory);
	}

	/**
	 * @return NULL
	 */
	public function testCreatePhpError()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\PHPErrorInterface',
			$this->factory->createPHPError()
		);
	}

	/**
	 * @return NULL
	 */
	public function testCreateAutoloader()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\AutoloadInterface',
			$this->factory->createAutoloader()
		);
	}
}

