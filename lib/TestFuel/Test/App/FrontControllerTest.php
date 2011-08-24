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
	 * When no error route is passed in the appfuel error route is used.
	 * @return null
	 */
	public function testConstructor()
	{
		$error = $this->front->getErrorRoute();
		$this->assertInstanceOf(
			'Appfuel\App\Route\ErrorRoute',
			$error
		);

		$errorRoute = $this->getMockRoute();	

		$this->assertNotEquals($errorRoute, $error);
		$front = new FrontController($errorRoute);
		$this->assertSame($errorRoute, $front->getErrorRoute());
	}

	/**
	 * A valid message is one that has a route and a request. When satisfied
	 * the method return true and the message has no errors
	 *
	 * @return null
	 */
	public function testIsSatisifiedByValid()
	{
		$msg = $this->getMockContext();
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
		$msg = $this->getMockContext();
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
		$msg = $this->getMockContext();

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
	 *
	 * @return	null
	 */	
	public function testExecute()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$ctr->expects($this->once())
			->method('execute')
			->will($this->returnValue($msg));

		$result = $this->front->execute($ctr, $msg);
		$this->assertSame($msg, $result);
		$this->assertFalse($msg->isError());
	}

	/**
	 * Test that the return message is not always the same message passed in.
	 * We want this because it allows the controller's execute to swap out
	 * a message before it is used for outputting
	 *
	 * @return null
	 */
	public function estExecuteReplacedMessage()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$returnMsg = new Context();
		$returnMsg->setError('the return message has an error');

		$ctr->expects($this->once())
			->method('execute')
			->will($this->returnValue($returnMsg));

		$result = $this->front->execute($ctr, $msg);
		$this->assertNotSame($msg, $result);
		$this->assertSame($returnMsg, $result);
		$this->assertTrue($returnMsg->isError());
	}

	/**
	 * Test what happens when the controller throws an execption
	 * 
	 * @return null
	 */
	public function testExecuteControllerThrowException()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$ctr->expects($this->once())
			->method('execute')
			->will($this->throwException(new Exception()));

		$result = $this->front->execute($ctr, $msg);
		$this->assertSame($msg, $result);
		$this->assertTrue($msg->isError());
	}

	/**
	 * The front controller initialize uses the action controller intialize.
	 * It also handles execeptions and checks the return of the initialize
	 * so that when a message object is returned it replaces the old message
	 * with the returned on. When the return type does not use a message 
	 * interface then its ignored. A valid initialize is one where the message
	 * setError has not been fired. We don't use a mock message in this test
	 * because we need to check if the error has been set
	 *
	 * @return	null
	 */	
	public function testInitialize()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$ctr->expects($this->once())
			->method('initialize')
			->will($this->returnValue($msg));

		$result = $this->front->initialize($ctr, $msg);
		$this->assertSame($msg, $result);
		$this->assertFalse($msg->isError());
	}

	/**
	 * Test that the return message is not always the same message passed in.
	 * We want this because it allows the controller's initialize to swap out
	 * a message before execute
	 *
	 * @return null
	 */
	public function estInitializeReplacedMessage()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$returnMsg = new  Context();
		$returnMsg->setError('the return message has an error');

		$ctr->expects($this->once())
			->method('initialize')
			->will($this->returnValue($returnMsg));

		$result = $this->front->initialize($ctr, $msg);
		$this->assertNotSame($msg, $result);
		$this->assertSame($returnMsg, $result);
		$this->assertTrue($returnMsg->isError());
	}

	/**
	 * Test what happens when the controller throws an execption
	 * 
	 * @return null
	 */
	public function testInitializeControllerThrowException()
	{
		$ctr = $this->getMockActionController();
		$msg = new Context();

		$ctr->expects($this->once())
			->method('initialize')
			->will($this->throwException(new Exception()));


		$result = $this->front->initialize($ctr, $msg);
		$this->assertSame($msg, $result);
		$this->assertTrue($msg->isError());
	}

	/**
	 * When no other builders are found false is returned
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderNoNamespaces()
	{
		$route = $this->getMockRoute();

		/* The builder object is instantiated in one of four namespaces,
		 * all of them will return empty strings so no builder object will
		 * be found
		 */
		$route->expects($this->once())
			  ->method('getActionNamespace')
			  ->will($this->returnValue(''));

		$route->expects($this->once())
			  ->method('getSubModuleNamespace')
			  ->will($this->returnValue(''));

		$route->expects($this->once())
			  ->method('getModuleNamespace')
			  ->will($this->returnValue(''));

		$route->expects($this->once())
			  ->method('getRootActionNamespace')
			  ->will($this->returnValue(''));
	
		$this->assertFalse($this->front->createActionBuilder($route));
	}

	/**
	 * @return null
	 */ 
	public function testCreateActionBuilderRootNamespace()
	{
		/* namespace to the known action controller */
		$namespace = '\Example\Action\Others\Dont\Exist';
		$route     = new ActionRoute('no/route', $namespace, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			'\Example\Action\ActionBuilder',
			$builder,
			'no other builders exist exception the root action builder'
		);	
	}

	/**
	 * Action\ModuleActionBuilder\ActionBuilder is the only object that exists
	 * below the Action\ActionBuilder so it should be found first
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderModuleNamespace()
	{
		$ns    = '\Example\Action\Error\Not\Exist';
		$route = new ActionRoute('no/route', $ns, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			'\Example\Action\Error\ActionBuilder',
			$builder,
			'Module builder should be found before root level builder'
		);	
	}

	/**
	 * Action\MyModule\MySubModule\ActionBuilder is the 
	 * only object that exists
	 * below the Action\ActionBuilder so it should be found first
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderSubModuleNamespace()
	{
		$ns = '\Example\Action\Error\Handler\None';

		$route     = new ActionRoute('no/route', $ns, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			'\Example\Action\Error\Handler\ActionBuilder',
			$builder,
			'Module builder should be found before root level builder'
		);	
	}

	/**
	 * Action\MyModule\MySubModule\MyAction\ActionBuilder should be the first
	 * builder found
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderActionNamespace()
	{
		$ns = '\Example\Action\Error\Handler\Invalid';

		$route     = new ActionRoute('no/route', $ns, 'public', 'json');
	
		$builder  = $this->front->createActionBuilder($route);
		
		$this->assertInstanceOf(
			'\Example\Action\Error\Handler\Invalid\ActionBuilder',
			$builder,
			'Action builder should be found before sub module level builder'
		);	
	}
}
