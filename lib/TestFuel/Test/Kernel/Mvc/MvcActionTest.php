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
	Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\KernelRegistry,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleViewTemplate,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\MvcActionDispatcher,
	Appfuel\Kernel\Mvc\MvcActionDispatcherInterface,
    Appfuel\Kernel\Mvc\Filter\FilterManager,
    Appfuel\Kernel\Mvc\Filter\FilterManagerInterface;

/**
 */
class MvcActionTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcAction
	 */
	protected $action = null;

	/**
	 * Keep a backup copy of the route map
	 * @var array
	 */
	protected $bkRoutes = null;

	/**
	 * Keep a backup copy of the kernel registry settings
	 * @var array
	 */
	protected $bkParams = null;

	/**
	 * Keep a backup copy of $_GET, $_POST, $_FILES, $_COOKIE, 
	 * and $_SERVER['argv']
	 * @var array
	 */
	protected $bkSuperGlobals = array();

	/**
	 * Route key injected into the controller. First paremeter
	 * @var string
	 */
	protected $route = null;

	/**
	 * Dispatcher injected into controller. Second parameter
	 * @var string
	 */
	protected $dispatcher = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->bkRoutes = KernelRegistry::getRouteMap();
		$this->bkParams = KernelRegistry::getParams();

	
		KernelRegistry::clearRouteMap();
		KernelRegistry::clearParams();

        $routeMap = array(
            'action-a' => 'TestFuel\Fake\Action\TestAction\ActionA',
            'action-b' => 'TestFuel\Fake\Action\TestAction\ActionB',
            'action-c' => 'TestFuel\Fake\Action\TestAction\ActionC',
        );
        KernelRegistry::setRouteMap($routeMap);
		$cli = null;
		if (isset($_SERVER['argv'])) {
			$cli = $_SERVER['argv'];
		}
		$this->bkSuperGlobals = array(
			'get'    => $_GET,
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE, 
			'argv'   => $cli
		);


		$this->dispatcher = new MvcActionDispatcher();
		$this->action = new MvcAction('action-a', $this->dispatcher);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		KernelRegistry::setRouteMap($this->bkRoutes);
		KernelRegistry::setParams($this->bkParams);

		$_GET    = $this->bkSuperGlobals['get'];
		$_POST   = $this->bkSuperGlobals['post'];
		$_FILES  = $this->bkSuperGlobals['files'];
		$_COOKIE = $this->bkSuperGlobals['cookie'];
		$cli = $this->bkSuperGlobals['argv'];
		if (null !== $cli) {
			$_SERVER['argv'] = $cli;
		}
		$this->dispatcher = null;
		$this->action = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionInterface',
			$this->action
		);
	}

	/**
	 * This is ment to be extended. If you don't extend it nothing will be
	 * allowed
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsContextAllowed()
	{
		$this->assertFalse($this->action->isContextAllowed(array()));
		$this->assertFalse($this->action->isContextAllowed(array('code')));
	}

	/**
	 * Process is the main method executed by the front controller. This
	 * method must be extended. If it is not the context nothing will hanppen
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testProcess()
	{
		$context = $this->getMock('Appfuel\Kernel\Mvc\AppContextInterface');
		$this->assertNull($this->action->process($context));
	}

	/**
	 * Three mvc actions have been designed for this test.
	 * TestFuel\Fake\Action\TestAction\(ActionA,ActionB,ActionC)
	 * Action A calls Action B and C and combines the results.
	 *
	 * @return	null
	 */
	public function testCallUri()
	{
		$context = $this->action->callUri('action-a', 'console');
		$expected = 'processed label-a=value-a label-b=value-b and '; 
		$expected .= 'processed label-a=value-c label-b=value-d';

		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContextInterface',
			$context
		);

		$view = $context->getView();
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);

		$result = $view->getAssigned('results');
		$this->assertEquals($expected, $result);
	}

	/**
	 * This show how to call another mvc action when you do not require any
	 * inputs to be passed
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCallWithNoInputs()
	{
		$context = $this->action->callWithNoInputs('action-a', 'console');
		$expected = 'processed label-a=value-a label-b=value-b and '; 
		$expected .= 'processed label-a=value-c label-b=value-d';
		
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContextInterface',
			$context
		);
		
		/* prove inputs are empty */
		$input = $context->getInput();
		$data = array(
			'get'    => array(),
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertEquals($data, $input->getAll());
		$view = $context->getView();
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);

		$result = $view->getAssigned('results');
		$this->assertEquals($expected, $result);
	}

	/**
	 * Call is the most manual way to call another mvc action, you have to 
	 * specify the uri, request method, input paramters and tell it if you
	 * want the uri as your get params (enabled by default)
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCall()
	{
		$strategy = 'console';
		$method   = 'post';
		$uri      = 'action-a/paramX/valueY';
		$params   = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
			'custom' => array('myparam' => 'myvalue'),
		);
		$context = $this->action->call($uri, $method, $params, $strategy);
		$expected = 'processed label-a=value-a label-b=value-b and '; 
		$expected .= 'processed label-a=value-c label-b=value-d';

		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContextInterface',
			$context
		);
	
		$params['get']['paramX'] = 'valueY';	
		/* prove inputs are empty */
		$input = $context->getInput();

		$this->assertEquals($params, $input->getAll());
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals('action-a', $context->getRoute());
		$this->assertEquals($strategy, $context->getStrategy());

		$view = $context->getView();
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);

		$result = $view->getAssigned('results');
		$this->assertEquals($expected, $result);
	}



}
