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
namespace TestFuel\Test\Kernel\Startup;

use StdClass,
	Exception,
	TestFuel\TestCase\FrameworkTestCase,
	Appfuel\Kernel\Startup\KernelInitTask;

/**
 * The abstract task encapsulates the common functionality of all start up 
 * tasks. We will test the ability to get registry keys, the ability to 
 * get the status, 
 */
class KernelInitTaskTest extends FrameworkTestCase
{
	/**
	 * System under test
	 * @var	KernalInitTask
	 */
	protected $task = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->task = new KernelInitTask();
		parent::setUp();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task = null;
		parent::tearDown();
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Kernel\Startup\StartupTaskInterface',
			$this->task
		);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Startup\StartupTaskAbstract',
			$this->task
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetRegistryKeys()
	{
		$expected = array(
            'include-path-action' => null,
            'include-path'		  => null,
            'display-errors'	  => null,
            'error-reporting'	  => null,
            'default-timezone'    => null,
        );

		$this->assertEquals($expected, $this->task->getRegistryKeys());
	}
}
