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
namespace Test\Appfuel\Stdlib\Autoload;

use Test\AfCase;
use Appfuel\AppBuilder;

/**
 * 
 */
class AppBuilderTest extends AfCase
{
	/**
	 * System Under Test
	 * @var Appfuel\AppBuilder
	 */
	protected $builder = NULL;

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
		$this->basePath = TEST_AF_BASE_PATH;
		$this->builder = new AppBuilder($this->basePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->builder);
	}

    /**
     * The constructor sets the base path as an immutable member
     * @return void
     */
    public function testConstructor()
    {
		$this->assertEquals(
			$this->basePath,
			$this->builder->getBasePath()
		);
    }

	/**
	 * This allows the builder to use a different class for resolving the
	 * which environment the server is on
	 *
	 * @return NULL
	 */
	public function testGetSetEnvClass()
	{
		$this->assertNull(
			$this->builder->getEnvClass(),
			'Initial value of getEnv should be NULL'
		);

		$class = 'someClass';
		$this->assertSame(
			$this->builder,
			$this->builder->setEnvClass($class),
			'Should be a fluent interface'
		);

		$this->assertEquals(
			$class, 
			$this->builder->getEnvClass(),
			'Should be the string just set'
		);
	}

	/**
	 * This allows the builder to override a factory class for create objects
	 *
	 * @return NULL
	 */
	public function testGetSetAppFactoryClass()
	{
		$this->assertNull(
			$this->builder->getAppFactoryClass(),
			'Initial value of getAppFactoryClass should be NULL'
		);

		$class = 'someClass';
		$this->assertSame(
			$this->builder,
			$this->builder->setAppFactoryClass($class),
			'Should be a fluent interface'
		);

		$this->assertEquals(
			$class, 
			$this->builder->getAppFactoryClass(),
			'Should be the string just set'
		);
	}

	/**
	 * This allows the builder to override an initializer class used to run
	 * initialization strategies.
	 *
	 * @return NULL
	 */
	public function testGetSetInitializerClass()
	{
		$this->assertNull(
			$this->builder->getInitializerClass(),
			'Initial value of getInitialize Class should be NULL'
		);

		$class = 'someClass';
		$this->assertSame(
			$this->builder,
			$this->builder->setInitializerClass($class),
			'Should be a fluent interface'
		);

		$this->assertEquals(
			$class, 
			$this->builder->getInitializerClass(),
			'Should be the string just set'
		);
	}
}

