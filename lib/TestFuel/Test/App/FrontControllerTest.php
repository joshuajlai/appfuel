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
namespace TestFuel\Test\App;

use Appfuel\App\Context,
	Appfuel\App\FrontController,
	Appfuel\Framework\Exception,
	Appfuel\App\Route\ActionRoute,
	TestFuel\TestCase\ControllerTestCase;

/**
 * The front controller handles all user requests, building the required action 
 * controller, dealing with errors.
 */
class FrontControllerTest extends ControllerTestCase
{
	/**
	 * System under test
	 * @var string
	 */
	protected $front = null;
	
	/**
	 * @return	null
	 */ 
	public function setUp()
	{
		$this->front = new FrontController();	
	}

	/**
	 * @return	null
	 */ 
	public function tearDown()
	{
		unset($this->front);
	}

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertTrue(true);
	}

}
