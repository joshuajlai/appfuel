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
	Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\AppContext,
	Appfuel\Kernel\KernelRegistry,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleViewTemplate,
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

	protected $backup = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backup = KernelRegistry::getRouteMap();
		$this->dispatcher = new MvcActionDispatcher();
		KernelRegistry::clearRouteMap();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		KernelRegistry::setRouteMap($this->backup);
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

	/**
	 * @depends	testInterface
	 */
	public function testProcessConsole()
	{
		$input = new AppInput('get');
		$context = new AppContext($input);

		$routeMap = array('my-key' => 'TestFuel\Fake\Action\User\Create');
		KernelRegistry::setRouteMap($routeMap);
		
		$view = $this->dispatcher->dispatch('my-key', 'app-console', $context);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\User\Create\ConsoleView',
			$view
		);
		$this->assertEquals('bar', $view->getAssigned('console-foo'));
		$this->assertEquals('value-a', $view->getAssigned('common-a'));
		$this->assertEquals('value-b', $view->getAssigned('common-b'));
	}

	/**
	 * @depends	testInterface
	 */
	public function testProcessAjax()
	{
		$input = new AppInput('get');
		$context = new AppContext($input);

		$routeMap = array('my-key' => 'TestFuel\Fake\Action\User\Create');
		KernelRegistry::setRouteMap($routeMap);
		
		$view = $this->dispatcher->dispatch('my-key', 'app-ajax', $context);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\User\Create\AjaxView',
			$view
		);
		$this->assertEquals('bar', $view->getAssigned('ajax-foo'));
		$this->assertEquals('value-a', $view->getAssigned('common-a'));
		$this->assertEquals('value-b', $view->getAssigned('common-b'));
	}

	/**
	 * @depends	testInterface
	 */
	public function testProcessHtml()
	{
		$input = new AppInput('get');
		$context = new AppContext($input);

		$routeMap = array('my-key' => 'TestFuel\Fake\Action\User\Create');
		KernelRegistry::setRouteMap($routeMap);
		
		$view = $this->dispatcher->dispatch('my-key', 'app-htmlpage', $context);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\User\Create\HtmlView',
			$view
		);
		$this->assertEquals('bar', $view->getAssigned('html-foo'));
		$this->assertEquals('value-a', $view->getAssigned('common-a'));
		$this->assertEquals('value-b', $view->getAssigned('common-b'));

	}
}
