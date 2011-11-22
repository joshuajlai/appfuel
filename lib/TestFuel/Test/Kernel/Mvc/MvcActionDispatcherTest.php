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
namespace TestFuel\Test\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcActionDispatcher;

/**
 */
class MvcActionDispatcherTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcActionDispatcher
	 */
	protected $dispatcher = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->dispatcher = new MvcActionDispatcher();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->dispatcher = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionDispatcherInterface',
			$this->dispatcher
		);
	}

	/**
	 * The MvcActionFactory is immutable but can be set through the 
	 * constructor. When not set it creates a default one
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testMvcActionFactory()
	{
		$factory = $this->dispatcher->getActionFactory();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionFactory',
			$factory
		);
	
		$factory = $this->getMock(
			'Appfuel\Kernel\Mvc\MvcActionFactoryInterface'
		);
		
		$dispatcher = new MvcActionDispatcher($factory);
		$this->assertSame($factory, $dispatcher->getActionFactory());
	}
}
