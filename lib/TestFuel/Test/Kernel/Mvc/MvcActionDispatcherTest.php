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
	Appfuel\Kernel\Mvc\RequestUri,
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
	 * When a route is set and not input is needed then the uri does not need
	 * to be set
	 *
	 * methods used: setRoute, noInputRequired, addAclCodes, buildContext
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_A()
	{
		$codes = array('my-code', 'your-code');
		$context = $this->dispatcher->setRoute('my-key')
								 ->noInputRequired()
								 ->addAclCodes($codes)
								 ->buildContext();
		$input = $context->getInput();
		$error = $context->getErrorStack();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppContext', $context);
		$this->assertEquals($codes, $context->getAclRoleCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);

		$expected = array(
			'get'    => array(), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertEquals($expected, $input->getAll());
		$this->assertInstanceOf('Appfuel\Error\ErrorStackInterface', $error);
	}

	/**
	 * I am using the route from a RequestUri along with its get paramters
	 * methods used: setUri, useUriForInputSource, addAclCodes, buildContext
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_B()
	{
		$route = 'my-key';
		$uri   = new RequestUri("$route/param1/value1/param2/value2");
		$codes = array('my-code', 'your-code');
		$context = $this->dispatcher->setUri($uri)
									->useUriForInputSource()
									->addAclCodes($codes)
									->buildContext();

		$input = $context->getInput();
		$error = $context->getErrorStack();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppContext', $context);
		$this->assertEquals($codes, $context->getAclRoleCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);

		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertEquals($expected, $input->getAll());
		$this->assertInstanceOf('Appfuel\Error\ErrorStackInterface', $error);
	}

	/**
	 * I am using the uristring not the object. I want the parameters in the
	 * input. For this test we will leave out the acl codes. Note this uri 
	 * string defineds the route key in the query string with the label 
	 * 'routekey'
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_C()
	{
		$uriString = 'param1/value1?routekey=my-key&param2=value2';
		$context   = $this->dispatcher->setUri($uriString)
									  ->useUriForInputSource()
									  ->buildContext();
	
		$input = $context->getInput();
		$error = $context->getErrorStack();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppContext', $context);
		$this->assertEquals(array(), $context->getAclRoleCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);

		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);	
		$this->assertEquals($expected, $input->getAll());
		$this->assertInstanceOf('Appfuel\Error\ErrorStackInterface', $error);
	}

	/**
	 * Tell the dispatcher to create the uri from the $_SERVER['REQUEST_URI']
	 * and use that for the input source
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_D()
	{
		$_SERVER['REQUEST_URI'] = 'my-key/param1/value1/param2/value2';
		$context = $this->dispatcher->useServerRequestUri()
									->useUriForInputSource()
									->buildContext();
	
		$input = $context->getInput();
		$error = $context->getErrorStack();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppContext', $context);
		$this->assertEquals(array(), $context->getAclRoleCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);

		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);	
		$this->assertEquals($expected, $input->getAll());
		$this->assertInstanceOf('Appfuel\Error\ErrorStackInterface', $error);		}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testSetUri_Failure($uri)
	{
		$context = $this->dispatcher->setUri($uri);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testSetRoute_Failure($route)
	{
		$context = $this->dispatcher->setRoute($route);
	}


	/**
	 * @depends	testInterface
	 */
	public function estProcessAjax()
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
	public function estProcessHtml()
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
