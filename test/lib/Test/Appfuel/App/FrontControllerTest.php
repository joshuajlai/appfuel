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
	Appfuel\App\FrontController;

/**
 * The front controller handles all user requests, building the required action 
 * controller, dealing with errors.
 */
class FrontControllerTest extends ParentTestCase
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
	 * When no error controller is passed in, the front controller will create
	 * and set one. We will be testing that the correct controller was set
	 *
	 * @return null
	 */
	public function testConstructor()
	{
		$error = $this->front->getErrorController();
		$this->assertInstanceOf(
			'Appfuel\App\Action\Error\Handler\Invalid\Controller',
			$error
		);

		$ctrClass = 'Appfuel\Framework\App\Action\ControllerInterface';
		$errorController = $this->getMock($ctrClass);	

		$this->assertNotEquals($errorController, $error);
		$front = new FrontController($errorController);
		$this->assertSame($errorController, $front->getErrorController());
	}
}

