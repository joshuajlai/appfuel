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
namespace TestFuel\Test\ClassLoader;

use StdClass,
	TestFuel\TestCase\FrameworkTestCase,
	Appfuel\ClassLoader\DependencyLoader;

/**
 *
 */
class DependencyLoaderTest extends FrameworkTestCase
{
	/**
	 * System under test
	 * @var DependencyLoader
	 */
	protected $loader = null;

	/**	
	 * @return	null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->loader = new DependencyLoader();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		parent::tearDown();
		$this->loader = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\DependencyLoaderInterface',
			$this->loader
		);
	}

	/**
	 * The autoloader is passed into the constructor and is immutable, if
	 * one is not supplied then the constructor creates one
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor()
	{
		$result = $this->loader->getLoader();
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\StandardAutoLoader',
			$result
		);

		$aLoader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
	
		$dependLoader = new DependencyLoader($aLoader);
		$this->assertSame($aLoader, $dependLoader->getLoader());	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */ 
	public function testDependencies()
	{
		$interface = "Appfuel\ClassLoader\ClassDependencyInterface";
		$this->assertEquals(array(), $this->loader->getDependencies());
		$depend1 = $this->getMock($interface);
		$depend2 = $this->getMock($interface);
		$depend3 = $this->getMock($interface);
		$this->assertSame(
			$this->loader,
			$this->loader->addDependency($depend1),
			'uses fluent interface'
		);

		$expected = array($depend1);
		$this->assertEquals($expected, $this->loader->getDependencies());

		$this->assertSame(
			$this->loader,
			$this->loader->addDependency($depend2),
			'uses fluent interface'
		);

		$expected = array($depend1, $depend2);
		$this->assertEquals($expected, $this->loader->getDependencies());

		$this->assertSame(
			$this->loader,
			$this->loader->addDependency($depend3),
			'uses fluent interface'
		);

		$expected = array($depend1, $depend2, $depend3);
		$this->assertEquals($expected, $this->loader->getDependencies());
	}
}
