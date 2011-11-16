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
	TestFuel\TestCase\BaseTestCase;

/**
 * The abstract task encapsulates the common functionality of all start up 
 * tasks. We will test the ability to get registry keys, the ability to 
 * get the status, 
 */
class StartupTaskAbstractTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AbstractStartupTask
	 */
	protected $task = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$class = 'Appfuel\Kernel\Startup\StartupTaskAbstract';
		$this->task = $this->getMockForAbstractClass($class);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task = null;
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
	}
}
