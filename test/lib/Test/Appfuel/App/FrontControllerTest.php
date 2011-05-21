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
namespace Test\Appfuel\App;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Route\ActionRoute,
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

	/**
	 * A valid message is one that has a route and a request. When satisfied
	 * the method return true and the message has no errors
	 *
	 * @return null
	 */
	public function testIsSatisifiedByValid()
	{
		$msg = $this->getMockBuilder('Appfuel\App\Message')
					->setMethods(array('isRoute', 'isRequest', 'setError'))
					->getMock();

		$msg->expects($this->any())
			->method('isRoute')
			->will($this->returnValue(true));

		$msg->expects($this->any())
			->method('isRequest')
			->will($this->returnValue(true));

	
		$msg->expects($this->never())
			->method('setError');

		$this->assertTrue($this->front->isSatisfiedBy($msg));
	}

	/**
	 * Missing route will return false and the message will have an error set
	 * @return null
	 */
	public function testIsSatisifiedByNoRoute()
	{
		$msg = $this->getMockBuilder('Appfuel\App\Message')
					->setMethods(array('isRoute', 'isRequest', 'setError'))
					->getMock();

		$msg->expects($this->any())
			->method('isRoute')
			->will($this->returnValue(false));

		$msg->expects($this->any())
			->method('isRequest')
			->will($this->returnValue(true));

		$msg->expects($this->once())
			->method('setError');

		$this->assertFalse($this->front->isSatisfiedBy($msg));
	}

	/**
	 * Missing request will return false and the message will have an error set
	 * @return null
	 */
	public function testIsSatisifiedByNoRequest()
	{
		$msg = $this->getMockBuilder('Appfuel\App\Message')
					->setMethods(array('isRoute', 'isRequest', 'setError'))
					->getMock();

		$msg->expects($this->any())
			->method('isRoute')
			->will($this->returnValue(true));

		$msg->expects($this->any())
			->method('isRequest')
			->will($this->returnValue(false));

		$msg->expects($this->once())
			->method('setError');

		$this->assertFalse($this->front->isSatisfiedBy($msg));
	}

	/**
	 * The action builder was designed to be extended. The first level
	 * that is checked is the action (controller) namespace. If a 
	 * class called ActionBuilder is found then it is instantiatied and
	 * returned 
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderRootNamespace()
	{
		/* namespace to the known action controller */
		$namespace = __NAMESPACE__ . '\Action\Does\Not\Exist';
		$route     = new ActionRoute('no/route', $namespace, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			__NAMESPACE__ . '\Action\ActionBuilder',
			$builder,
			'no other builders exist exception the root action builder'
		);	
	}

}

