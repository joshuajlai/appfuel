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
            'my-route' => 'TestFuel\Fake\Action\TestFront\ActionA'
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
		$this->action = new MvcAction('my-route', $this->dispatcher);
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
}
