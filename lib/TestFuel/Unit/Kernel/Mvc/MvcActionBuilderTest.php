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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcRouteDetail,
	Appfuel\Kernel\Mvc\MvcActionBuilder;

/**
 */
class MvcActionBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcActionBuilder
	 */
	protected $builder = null;

	/**
	 * @var string
	 */
	protected $actionClassName = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->actionClassName = MvcActionBuilder::getActionClassName();
		$this->builder = new MvcActionBuilder;
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$name = $this->actionClassName;
		$this->actionClassName = MvcActionBuilder::setActionClassName($name);
		$this->builder = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionBuilderInterface',
			$this->builder
		);

		$loader = $this->builder->getClassLoader();
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\StandardAutoLoader',
			$this->builder->getClassLoader()
		);

		$className = MvcActionBuilder::getActionClassName();
		$this->assertEquals('ActionController', $className);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetActionClassName()
	{
		$name = 'MyControllerClass';
		$this->assertNull(MvcActionBuilder::setActionClassName($name));
		$this->assertEquals($name, MvcActionBuilder::getActionClassName());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetActionClassNameEmptyString_Failure($name)
	{
		MvcActionBuilder::setActionClassName($name);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetActionClassNameNotString_Failure($name)
	{
		MvcActionBuilder::setActionClassName($name);
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetClassLoader()
	{
		$loader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
		$this->assertSame(
			$this->builder, 
			$this->builder->setClassLoader($loader)
		);
		$this->assertSame($loader, $this->builder->getClassLoader());
	}


}
