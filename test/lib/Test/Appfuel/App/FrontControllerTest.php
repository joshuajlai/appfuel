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
	 * When no other builders are found false is returned
	 *
	 * @return null
	 */ 
	public function testCreateActionBuilderNoNamespaces()
	{
		/* namespace to the known action controller */
		$routeInterface = 'Appfuel\Framework\App\Route\RouteInterface';
		$methods = array(
			'getRouteString',
			'getAccessPolicy',
			'getResponseType',
			'getActionNamespace',
			'getSubModuleNamespace',
			'getModuleNamespace',
			'getRootActionNamespace'
		);
		$route = $this->getMockBuilder($routeInterface)
					  ->setMethods($methods)
					  ->getMock();

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
		$namespace = __NAMESPACE__ . '\MyRootAction\Others\Dont\Exist';
		$route     = new ActionRoute('no/route', $namespace, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			__NAMESPACE__ . '\MyRootAction\ActionBuilder',
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
		$ns    = __NAMESPACE__ . '\MyRootAction\MyModule\Not\Exist';
		$route = new ActionRoute('no/route', $ns, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			__NAMESPACE__ . '\MyRootAction\MyModule\ActionBuilder',
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
		$ns = __NAMESPACE__ . '\MyRootAction\MyModule\MySubModule\None';

		$route     = new ActionRoute('no/route', $ns, 'public', 'json');
	
		$builder = $this->front->createActionBuilder($route);
		$this->assertInstanceOf(
			__NAMESPACE__ . '\MyRootAction\MyModule\MySubModule\ActionBuilder',
			$builder,
			'Module builder should be found before root level builder'
		);	
	}

}

