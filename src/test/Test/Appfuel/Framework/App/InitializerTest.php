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
namespace Test\Appfuel\Framework\App;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Framework\App\Initializer;

/**
 * 
 */
class InitializerTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Framework\App\Initializer
	 */
	protected $initializer = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->basePath = $this->getBasePath();
		
		$this->initializer = new Initializer($this->basePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->initializer);
	}

	/**
	 * The base path is passed into the constructor and is immutable
	 * 
	 * @return null
	 */
	public function testGetBasePath()
	{
		$this->assertEquals(
			$this->basePath,
			$this->initializer->getBasePath(),
			'Should be the same path passed into the constructor'
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetCreateFactory()
	{
		$this->assertNull(
			$this->initializer->getFactory(),
			'Initial value of getFactory should be null'
		);

		$factory = $this->getMock(
			'\\Appfuel\\Framework\\App\\FactoryInterface'
		);

		$result = $this->initializer->setFactory($factory);
		$this->assertSame(
			$this->initializer,
			$result,
			'Should support fluent interface'
		);

		$this->assertSame(
			$factory,
			$this->initializer->getFactory(),
			'Should be the factory that was set'
		);

		$class  = 'Appfuel\\Framework\\App\\Factory';
		$result = $this->initializer->createFactory($class);
		$this->assertInstanceOf(
			$class,
			$result,
			'should create the appfuel app factory'
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetRegisterAutoloader()
	{
		$this->assertNull(
			$this->initializer->getAutoloader(),
			'Initial value of getAutoloader should be null'
		);

		$loader = $this->getMock(
			'\\Appfuel\\Framework\\App\\AutoloadInterface'
		);

		$result = $this->initializer->setAutoloader($loader);
		$this->assertSame(
			$this->initializer,
			$result,
			'Should support fluent interface'
		);

		$this->assertSame(
			$loader,
			$this->initializer->getAutoloader(),
			'Should be the Autoloader that was set'
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetPHPError()
	{
		$this->assertNull(
			$this->initializer->getPHPError(),
			'Initial value of getPHPError should be null'
		);

		$error = $this->getMock(
			'\\Appfuel\\Framework\\App\\PHPErrorInterface'
		);

		$result = $this->initializer->setPHPError($error);
		$this->assertSame(
			$this->initializer,
			$result,
			'Should support fluent interface'
		);

		$this->assertSame(
			$error,
			$this->initializer->getPHPError(),
			'Should be the Error Object that was set'
		);
	}


}

