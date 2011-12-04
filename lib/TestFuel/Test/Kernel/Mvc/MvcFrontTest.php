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
	Appfuel\Kernel\Mvc\MvcFront,
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
class MvcFrontTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcFrontTest
	 */
	protected $front = null;

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
	 * @return null
	 */
	public function setUp()
	{
		$this->bkRoutes = KernelRegistry::getRouteMap();
		$this->bkParams = KernelRegistry::getParams();

		$this->front = $this->createMvcFront();
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
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		KernelRegistry::setRouteMap($this->bkRoutes);
		KernelRegistry::setParams($this->bkParams);
		$this->front = null;

		$_GET    = $this->bkSuperGlobals['get'];
		$_POST   = $this->bkSuperGlobals['post'];
		$_FILES  = $this->bkSuperGlobals['files'];
		$_COOKIE = $this->bkSuperGlobals['cookie'];
		$cli = $this->bkSuperGlobals['argv'];
		if (null !== $cli) {
			$_SERVER['argv'] = $cli;
		}
	}

	/**
	 * @param	MvcDispatcherInterface $dispatcher
	 * @param	FilterManagerInterface $manager
	 * @return	MvcFront
	 */
	public function createMvcFront(MvcActionDispatcherInterface $dp = null,
								   FilterManagerInterface $manager = null)
	{
		return new MvcFront($dp, $manager);
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcFrontInterface',
			$this->front
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDispatcher()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionDispatcher',
			$this->front->getDispatcher()
		);

		$dispatcher = $this->getMock(
			'Appfuel\Kernel\Mvc\MvcActionDispatcherInterface'
		);
		$front = $this->createMvcFront($dispatcher);
		$this->assertSame($dispatcher, $front->getDispatcher());
	}
    /**
     * @depends testInterface
     * @return  null
     */
	public function testFilterManager()
	{
        $this->assertInstanceOf(
            'Appfuel\Kernel\Mvc\Filter\FilterManager',
            $this->front->getFilterManager()
        );

        $manager = $this->getMock(
            'Appfuel\Kernel\Mvc\Filter\FilterManagerInterface'
        );
		
		$front = $this->createMvcFront(null, $manager);
		$this->assertSame($manager, $front->getFilterManager());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDispatcherAndFilterManager()
	{
		$dispatcher = $this->getMock(
			'Appfuel\Kernel\Mvc\MvcActionDispatcherInterface'
		);

	    $manager = $this->getMock(
            'Appfuel\Kernel\Mvc\Filter\FilterManagerInterface'
        );

		$front = $this->createMvcFront($dispatcher, $manager);
		$this->assertSame($dispatcher, $front->getDispatcher());
		$this->assertSame($manager, $front->getFilterManager());	
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetOutputEngineForConsole()
	{
		$this->assertNull($this->front->getOutputEngine());
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
	
		$this->front->setStrategy('console');	
		$this->assertSame(
			$this->front,
			$this->front->setOutputEngine($output),
			'uses fluent interface'
		);
		$this->assertSame($output, $this->front->getOutputEngine());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetOutputEngineForAjax()
	{
		$this->assertNull($this->front->getOutputEngine());
		$output = $this->getMock('Appfuel\Http\HttpOutputInterface');
	
		$this->front->setStrategy('ajax');	
		$this->assertSame(
			$this->front,
			$this->front->setOutputEngine($output),
			'uses fluent interface'
		);
		$this->assertSame($output, $this->front->getOutputEngine());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetOutputEngineForHtml()
	{
		$this->assertNull($this->front->getOutputEngine());
		$output = $this->getMock('Appfuel\Http\HttpOutputInterface');
	
		$this->front->setStrategy('html');	
		$this->assertSame(
			$this->front,
			$this->front->setOutputEngine($output),
			'uses fluent interface'
		);
		$this->assertSame($output, $this->front->getOutputEngine());
	}

	/**
	 * It is a runtime exception to use an HttpOutputInterface with a console
	 * strategy
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetOuputEngineConsoleWithHttp()
	{
		$output = $this->getMock('Appfuel\Http\HttpOutputInterface');
		$this->front->setStrategy('console')
					->setOutputEngine($output);
		
	}

	/**
	 * It is a runtime exception to use an ConsoleOutputInterface with a html
	 * strategy
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetOuputEngineHtmlWithConsoleOutput()
	{
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$this->front->setStrategy('html')
					->setOutputEngine($output);
		
	}

	/**
	 * It is a runtime exception to use an ConsoleOutputInterface with a ajax
	 * strategy
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetOuputEngineAjaxWithConsoleOutput()
	{
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$this->front->setStrategy('ajax')
					->setOutputEngine($output);
		
	}

	/**
	 * It is a runtime exception to set the output engine for the strategy is
	 * set
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetOuputEngineBeforeStrategy()
	{
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$this->front->setOutputEngine($output);
	}

	/**
	 * Inject the dispatcher so you can test the strategy as it is set
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetStrategy()
	{
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		$this->assertSame($front, $front->setStrategy('html'));
		$this->assertEquals('html', $dispatcher->getStrategy());
		
		$this->assertSame($front, $front->setStrategy('console'));
		$this->assertEquals('console', $dispatcher->getStrategy());

		$this->assertSame($front, $front->setStrategy('ajax'));
		$this->assertEquals('ajax', $dispatcher->getStrategy());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRouteThatIsMappedWithDispatcher()
	{
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->setRoute('my-route'));
		$this->assertEquals('my-route', $dispatcher->getRoute());
	}

	/**
	 * @expectedException	Appfuel\Kernel\Mvc\RouteNotFoundException
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRouteThatIsNotMappedWithDispatcher()
	{
		$this->front->setRoute('route-not-found');
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddAclCodesWithDispatcher()
	{	
		$codes = array('my-code', 'your-code');
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->addAclCodes($codes));

		$context = $dispatcher->setRoute('my-route')
							  ->setStrategy('console')
							  ->noInputRequired()
							  ->buildContext();
		$this->assertEquals($codes, $context->getAclCodes());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddAclCodeWithDispatcher()
	{	
		$code = 'my-code';
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->addAclCode($code));

		$context = $dispatcher->setRoute('my-route')
							  ->setStrategy('console')
							  ->noInputRequired()
							  ->buildContext();
		$this->assertEquals(array($code), $context->getAclCodes());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetUriWithStringWithDispatcher()
	{	
		$uriString  = 'my-route/param1/value1';
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->setUri($uriString));
		$_SERVER['REQUEST_METHOD'] = 'get';
		$useUri = true;
		$context = $dispatcher->setStrategy('console')
							  ->defineInputFromSuperGlobals($useUri)
							  ->buildContext();

		$this->assertEquals('my-route', $dispatcher->getRoute());
		$this->assertEquals('my-route', $context->getRoute());
		$input = $context->getInput();
		$expected = array('param1' => 'value1');
		$this->assertEquals($expected, $input->getAll('get'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetUriWithRequestUriWithDispatcher()
	{	
		$uriString  = 'my-route/param1/value1';
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->setUri($uriString));

		$_SERVER['REQUEST_METHOD'] = 'get';
		$useUri = true;
		$context = $dispatcher->setStrategy('console')
							  ->defineInputFromSuperGlobals($useUri)
							  ->buildContext();

		$this->assertEquals('my-route', $dispatcher->getRoute());
		$input = $context->getInput();
		$expected = array('param1' => 'value1');
		$this->assertEquals($expected, $input->getAll('get'));
	}


	/**
	 * This will look for the uri string in the super global 
	 * $_SERVER['REQUEST_URI'] and use that call setUri
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testUseServerRequestUri()
	{
		$_SERVER['REQUEST_URI'] = 'my-route/paramX/valueY';
		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->useServerRequestUri());

		/* use all the super global for inputs with the useUri flag enabled
		 * so the 'get' parameters will be taken for the uri create. then
		 * we can check if thoses params are the ones we used in the uri string
		 */
		$_SERVER['REQUEST_METHOD'] = 'get';
		$useUri = true;
		$context = $dispatcher->setStrategy('ajax')
							  ->defineInputFromSuperGlobals($useUri)
							  ->buildContext();

		$this->assertEquals('my-route', $dispatcher->getRoute());
		$input = $context->getInput();
		$expected = array('paramX' => 'valueY');
		$this->assertEquals($expected, $input->getAll('get'));
		$this->assertEquals('get', $input->getMethod());	
	}

	/**
	 * defineInput allows you to determine what request method (get|post|cli)
	 * and all the input paramters for (get|post|files|cookie|argv)
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputUriDisabled()
	{
		$params = array(
			'get'	 => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
			'custom' => array('paramX' => 'valueY')
		);
		$method = 'get';
		$useUri = false;

		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame(
			$front, 
			$front->defineInput($method, $params, $useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							->setRoute('my-route')
							->buildContext();
		
		$input = $context->getInput();	
		$this->assertEquals($params, $input->getAll());
		$this->assertEquals('get', $input->getMethod());	
	}

	/**
	 * When used with the useUri flag enabled the 'get' params are derived from
	 * the uri object
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputUriEnabled()
	{
		$uri = new RequestUri('my-route/param1/value1');
		$params = array(
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
			'custom' => array('paramX' => 'valueY')
		);
		$method = 'get';
		$useUri = true;

		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->setUri($uri));
		$this->assertSame(
			$front, 
			$front->defineInput($method, $params, $useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							->buildContext();
		
		$input = $context->getInput();	
		$this->assertEquals('my-route', $dispatcher->getRoute());
		
		$params['get'] = array('param1' => 'value1');
		$this->assertEquals($params, $input->getAll());
		$this->assertEquals('get', $input->getMethod());	
	}

	/**
	 * When used with the useUri flag enabled the 'get' and you have defined
	 * your own 'get' params then the two will be merged
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputUriMerged()
	{
		$uri = new RequestUri('my-route/param6/value6/paramA/valueC');
		$params = array(
			'get'    => array('param1' => 'value1', 'paramA' => 'valueB'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
			'custom' => array('paramX' => 'valueY')
		);
		$method = 'get';
		$useUri = true;

		$dispatcher = new MvcActionDispatcher();
		$front = $this->createMvcFront($dispatcher);
		
		$this->assertSame($front, $front->setUri($uri));
		$this->assertSame(
			$front, 
			$front->defineInput($method, $params, $useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							->buildContext();
		
		$input = $context->getInput();	
		$this->assertEquals('my-route', $dispatcher->getRoute());
		
		$params['get'] = array(
			'param1' => 'value1',
			'paramA' => 'valueC',
			'param6' => 'value6'
		);
		$this->assertEquals($params, $input->getAll());
		$this->assertEquals('get', $input->getMethod());	
	}

	/**
	 * This method will create an AppInput from the super globals:
	 * 'get'    => $_GET,
	 * 'post'   => $_POST,
	 * 'files'  => $_FILES,
	 * 'cookie' => $_COOKIE,
	 * 'argv'   => $_SERVER['argv']
	 *  
	 * the request method is taken from $_SERVER['REQUEST_METHOD']
	 *
	 * The final input should be a post with the exact same params as 
	 * assigned to $params
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsUriDisabled()
	{
		$params = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
		);
		$_GET    = $params['get'];
		$_POST   = $params['post'];
		$_FILES  = $params['files'];
		$_COOKIE = $params['cookie'];
		$_SERVER['argv'] = $params['argv'];

		$_SERVER['REQUEST_METHOD'] = 'post';
		
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$useUri = false;

		$this->assertSame(
			$front,	
			$front->defineInputFromSuperGlobals($useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							  ->setRoute('my-route')
							  ->buildContext();

		$input = $context->getInput();
		$this->assertEquals('post', $input->getMethod());
		$this->assertEquals($params, $input->getALl());
	}

	/**
	 * When $useUri flag is enabled (the default) then the 'get' params 
	 * are derived from the uri string and not the $_GET super global
	 *
	 * 'post'   => $_POST,
	 * 'files'  => $_FILES,
	 * 'cookie' => $_COOKIE,
	 * 'argv'   => $_SERVER['argv']
	 *  
	 * the request method is taken from $_SERVER['REQUEST_METHOD']
	 *
	 * The final input should be a post with the exact same params as 
	 * assigned to $params
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsUriEnabledAndSet()
	{
		$uriString = 'my-route/param1/value1';
		$params = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
		);
		$_GET    = array();
		$_POST   = $params['post'];
		$_FILES  = $params['files'];
		$_COOKIE = $params['cookie'];
		$_SERVER['argv'] = $params['argv'];

		$_SERVER['REQUEST_METHOD'] = 'get';
		
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$useUri = true;

		$this->assertSame($front, $front->setUri($uriString));
		$this->assertSame(
			$front,	
			$front->defineInputFromSuperGlobals($useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							  ->buildContext();

		$input = $context->getInput();
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals($params, $input->getAll());
	}

	/**
	 * When $useUri flag is enabled (the default) and the uri is not set then
	 * the dispatcher will try to get it from the $_SERVER['REQUEST_URI']
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsUriEnabledAndNotSet()
	{
		$_SERVER['REQUEST_URI'] = 'my-route/param1/value1';
		$params = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
		);
		$_GET    = array();
		$_POST   = $params['post'];
		$_FILES  = $params['files'];
		$_COOKIE = $params['cookie'];
		$_SERVER['argv'] = $params['argv'];

		$_SERVER['REQUEST_METHOD'] = 'get';
		
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$useUri = true;
		$this->assertSame(
			$front,	
			$front->defineInputFromSuperGlobals($useUri)
		);

		$context = $dispatcher->setStrategy('ajax')
							  ->buildContext();

		$input = $context->getInput();
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals($params, $input->getAll());
	}

	/**
	 * When $useUri flag is enabled (the default) and the uri is not set then
	 * the dispatcher will try to get it from the $_SERVER['REQUEST_URI']
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testDefineInputFromSuperGlobalsUriEnabledAndNotSet_Failure()
	{
		unset($_SERVER['REQUEST_URI']);
		$params = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5'),
		);
		$_GET    = array();
		$_POST   = $params['post'];
		$_FILES  = $params['files'];
		$_COOKIE = $params['cookie'];
		$_SERVER['argv'] = $params['argv'];

		$_SERVER['REQUEST_METHOD'] = 'get';
		
		$useUri = true;
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$front->defineInputFromSuperGlobals($useUri);
	}

	/**
	 * This method will use the uri for the input source, meaning the 
	 * request method is 'get' and all parameters will be put into the 
	 * 'get' 
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testUseUriForInputWithUri()
	{
		$uri = new RequestUri('my-route/param6/value6/paramA/valueC');
		$params = array(
			'param6' => 'value6',
			'paramA' => 'valueC'
		);
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$front->setUri($uri);
		$this->assertSame($front, $front->useUriForInputSource());

		$context = $dispatcher->setStrategy('ajax')
							  ->buildContext();
		$this->assertEquals('my-route', $dispatcher->getRoute());
		
		$input = $context->getInput();
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals($params, $input->getAll('get'));
	}

	/**
	 * Its a runtime exception use Uri for an input source without setting
	 * the uri
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testUseUriForInputWithUriNoObject_Failure()
	{
		$params = array(
			'param6' => 'value6',
			'paramA' => 'valueC'
		);
		
		/* note how this has no effect, with useUriForInputSource the 
		 * input method is always get
		 */
		$_SERVER['REQUEST_METHOD'] = 'post';
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$front->useUriForInputSource();
	}

	/**
	 * This will configure the dispatcher to build an AppInput with a 
	 * method 'get' and no paramters
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testNoInputRequired()
	{
		$dispatcher = new MvcActionDispatcher();
		$front  = $this->createMvcFront($dispatcher);
		$this->assertSame($front, $front->noInputRequired());
		
		$context = $dispatcher->setStrategy('console')
							  ->setRoute('my-route')
							  ->buildContext();
		
		$params = array(
			'get'	 => array(),
			'post'	 => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'	 => array()
		);
		$input = $context->getInput();
		$this->assertEquals('get',	$input->getMethod());
		$this->assertEquals($params,$input->getAll()); 
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRunDispatchConsoleNoInputNoFilters()
	{
		$uriString = 'my-route/param1/value1';
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$render = function($data) {
			echo $data;
		};

		$output->expects($this->once())
			  ->method('render')
			  ->will($this->returnCallback($render));

		$result = $this->front->runConsoleUri($uriString, $output);
		$this->assertEquals(200, $result);
		$this->expectOutputString('this action has been executed');
	}

	/**
	 * This prefilter adds the label 'my-assignment' with the value 
	 * 'value 1 2 3' to the view. Then controller then appends its text
	 * 'this action as been executed' to that label
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRunDispatchConsoleWithPreFilter()
	{
		$uriString = 'my-route/param1/value1';
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$render = function($data) {
			echo $data;
		};

		$filters = array('TestFuel\Fake\Action\TestFront\AddLabelFilter');
		KernelRegistry::addParam('intercepting-filters', $filters);

		$output->expects($this->once())
			  ->method('render')
			  ->will($this->returnCallback($render));

		$result = $this->front->runConsoleUri($uriString, $output);
		$expected = 'value 1 2 3 this action has been executed';
		$this->assertEquals(200, $result);
		$this->expectOutputString($expected);
	}

	/**
	 * The PrependLabelFilter will prepend '4 5 6' to 'value 1 2 3' from the
	 * first filter and the action controller will append its value on to it
	 * 
	 *  
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRunDispatchConsoleWith2PreFilter()
	{
		$uriString = 'my-route/param1/value1';
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$render = function($data) {
			echo $data;
		};

		$filters = array(
			'TestFuel\Fake\Action\TestFront\AddLabelFilter',
			'TestFuel\Fake\Action\TestFront\PrependLabelFilter',
		);
		KernelRegistry::addParam('intercepting-filters', $filters);

		$output->expects($this->once())
			  ->method('render')
			  ->will($this->returnCallback($render));

		$result = $this->front->runConsoleUri($uriString, $output);
		$expected = '4 5 6 value 1 2 3 this action has been executed';
		$this->assertEquals(200, $result);
		$this->expectOutputString($expected);
	}

	/**
	 * The post filter will replace all spaces with ':' 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRunDispatchConsoleWithPreAndPostFilters()
	{
		$uriString = 'my-route/param1/value1';
		$output = $this->getMock('Appfuel\Console\ConsoleOutputInterface');
		$render = function($data) {
			echo $data;
		};

		$filters = array(
			'TestFuel\Fake\Action\TestFront\AddLabelFilter',
			'TestFuel\Fake\Action\TestFront\PrependLabelFilter',
			'TestFuel\Fake\Action\TestFront\ReplaceSpacesFilter',
		);
		KernelRegistry::addParam('intercepting-filters', $filters);

		$output->expects($this->once())
			  ->method('render')
			  ->will($this->returnCallback($render));

		$result = $this->front->runConsoleUri($uriString, $output);
		$expected = '4:5:6:value:1:2:3:this:action:has:been:executed';
		$this->assertEquals(200, $result);
		$this->expectOutputString($expected);
	}
}
