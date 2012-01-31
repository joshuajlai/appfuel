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
namespace TestFuel\Unit\Kernel\Kernel;

use Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\MvcContextBuilder,
	TestFuel\TestCase\ControllerTestCase;

/**
 * Test the ability for the builder to create request uri with its different
 * configurations (createRequestUri, setUri, useServerRequestUri, useUriString)
 * Also test the ability to create AppInput with its different configurations
 * (setInput, buildInputFromDefault, defineInputAs, createInput) Also test
 * the ability to set the error stack.
 */
class MvcContextBuilderTest extends ControllerTestCase
{
    /**
     * System under test
     * @var ContextBuilder
     */
    protected $builder = null;
	
	/**
	 * @var array
	 */
	protected $serverBk = null;
    
	/**
     * @return null
     */
    public function setUp()
    {
		$this->serverBk = $_SERVER;
		$this->builder = new MvcContextBuilder();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
		$_SERVER = $this->serverBk;
		$this->builder = null;
    }

	public function setupSuperGlobals($method, $uri)
	{
		$_SERVER['REQUEST_METHOD'] = $method;
		$_SERVER['REQUEST_URI'] = 'my-route/param1/value1';
		$_POST   = array('param2' => 'value 2');
		$_FILES  = array('param3' => 'value 3');
		$_COOKIE = array('param4' => 'value 4');
	
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContextBuilderInterface',
			$this->builder
		);
		
		$this->assertNull($this->builder->getStrategy());
		$this->assertNull($this->builder->getRouteKey());
		$this->assertNull($this->builder->getUri());
		$this->assertNull($this->builder->getInput());
		$this->assertEquals(array(), $this->builder->getAclCodes());
		$this->assertNull($this->builder->getView());
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\StandardAutoLoader',
			$this->builder->getClassLoader()
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetClassLoader()
	{
		$loader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
		$this->assertNotSame($loader, $this->builder->getClassLoader());

		$this->assertSame(
			$this->builder, 
			$this->builder->setClassLoader($loader)
		);
	
		$this->assertSame($loader, $this->builder->getClassLoader());
	}

	/**
	 * @dataProvider	provideNonEmptyStrings
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testGetSetStrategy($strategy)
	{
		$this->assertSame(
			$this->builder, 
			$this->builder->setStrategy($strategy)
		);
		$this->assertEquals($strategy, $this->builder->getStrategy());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testGetSetStrategyEmptyString($strategy)
	{
		$this->assertSame(
			$this->builder, 
			$this->builder->setStrategy($strategy)
		);
		$this->assertEquals($strategy, $this->builder->getStrategy());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetStrategy_Failure($strategy)
	{
		$this->builder->setStrategy($strategy);
	}

	/**
	 * @dataProvider	provideNonEmptyStrings
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testGetSetRouteKey($route)
	{
		$this->assertSame(
			$this->builder, 
			$this->builder->setRouteKey($route)
		);
		$this->assertEquals($route, $this->builder->getRouteKey());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testGetSetRouteKeyEmptyString($route)
	{
		$this->assertSame(
			$this->builder, 
			$this->builder->setRouteKey($route)
		);
		$this->assertEquals($route, $this->builder->getRouteKey());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetRouteKey_Failure($route)
	{
		$this->builder->setRouteKey($route);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateRequestUri()
	{
		$uriString = 'my-route/param1/value1';
		$uri = $this->builder->createUri($uriString);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUri',
			$uri
		);
	}

	/**
	 * @depends	testCreateRequestUri
	 * @return	null
	 */
	public function testGetSetUriInterface()
	{
		$uri = $this->getMock('Appfuel\Kernel\Mvc\RequestUriInterface');
		$this->assertSame(
			$this->builder,
			$this->builder->setUri($uri),
			'uses fluent interface'
		);
		$this->assertSame($uri, $this->builder->getUri());	
	}

	/**
	 * @dataProvider	provideNonEmptyStrings
	 * @depends			testCreateRequestUri
	 * @return			null
	 */
	public function testGetSetUriString($str)
	{
		$this->assertSame(
			$this->builder,
			$this->builder->setUri($str),
			'uses fluent interface'
		);
	
		$uri = $this->builder->getUri();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\RequestUri', $uri);

		$this->assertEquals($str, $uri->getUriString());
	}

	/**
	 * @dataProvider	provideEmptyStrings
	 * @depends			testCreateRequestUri
	 * @return			null
	 */
	public function testGetSetUriEmptyString($str)
	{
		$this->assertSame(
			$this->builder,
			$this->builder->setUri($str),
			'uses fluent interface'
		);
	
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUri',
			$this->builder->getUri()
		);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testCreateRequestUri
	 * @return				null
	 */
	public function testGetSetUri_Failure($str)
	{
		$this->builder->setUri($str);
	}

	/**
	 * @depends	testGetSetUriInterface
	 * @return	null
	 */
	public function testUseServerRequestUri()
	{
		$uriString = 'my-route/param1/value1';
		$_SERVER['REQUEST_URI'] = $uriString;

		$this->assertSame(
			$this->builder,
			$this->builder->useServerRequestUri(),
			'uses fluent interface'
		);
		
		$uri = $this->builder->getUri();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUriInterface',
			$uri,
			'Uri object built from the SERVER[REQUEST_URI]'
		);

		$this->assertEquals($uriString, $uri->getUriString());			
	}

	/**
	 * @depends	testGetSetUriInterface
	 * @return	null
	 */
	public function testUseServerRequestUriWithQueryString()
	{
		$uriString = 'routekey=my-route&param1=value1';
		$_SERVER['QUERY_STRING'] = $uriString;

		$this->assertSame(
			$this->builder,
			$this->builder->useServerRequestUri(),
			'uses fluent interface'
		);
		
		$uri = $this->builder->getUri();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUriInterface',
			$uri,
			'Uri object built from the SERVER[QUERY_STRING]'
		);

		$this->assertEquals("?{$uriString}", $uri->getUriString());			
	}

	/**
	 * When both query string and request uri are present the query stirng
	 * will be select
	 *
	 * @depends	testGetSetUriInterface
	 * @return	null
	 */
	public function testUseServerRequestUriBothQueryStringRequestUri()
	{
		$rString = 'other-route/param2/value2';
		$qString = 'routekey=my-route&param1=value1';
		$_SERVER['QUERY_STRING'] = $qString;
		$_SERVER['REQUEST_URI']  = $rString;

		$this->assertSame(
			$this->builder,
			$this->builder->useServerRequestUri(),
			'uses fluent interface'
		);
		
		$uri = $this->builder->getUri();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUriInterface',
			$uri,
			'Uri object built from the SERVER[QUERY_STRING]'
		);

		$this->assertEquals("?{$qString}", $uri->getUriString());			
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetInput()
	{
		$input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');
		$this->assertSame(
			$this->builder,
			$this->builder->setInput($input),
			'uses fluent interface'
		);
		$this->assertSame($input, $this->builder->getInput());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateInput()
	{
		$inputClass = 'Appfuel\Kernel\Mvc\AppInput';
		$input = $this->builder->createInput('get');
		$expected = array(
			'get'	=> array(),
			'post'	=> array(),
			'files' => array(),
			'cookie' => array(),
			'argv'  => array()
		);
		$this->assertEquals($expected, $input->getAll());
		$this->assertInstanceOf($inputClass, $input);
		
		$input = $this->builder->createInput('post');
		$this->assertInstanceOf($inputClass, $input);
		$this->assertEquals($expected, $input->getAll());

		$input = $this->builder->createInput('cli');
		$this->assertInstanceOf($inputClass, $input);
		$this->assertEquals($expected, $input->getAll());


		$params = array(
			'get'	 => array('param1' => 'value1'),
			'post'	 => array('param2' => 'value2'),
			'files'  => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv'   => array('param5' => 'value5')
		);
		$input = $this->builder->createInput('get', $params);
		$this->assertInstanceOf($inputClass, $input);
		$this->assertEquals($params, $input->getAll());

		$input = $this->builder->createInput('post', $params);
		$this->assertInstanceOf($inputClass, $input);
		$this->assertEquals($params, $input->getAll());

		$input = $this->builder->createInput('cli', $params);
		$this->assertInstanceOf($inputClass, $input);
		$this->assertEquals($params, $input->getAll());
	}

	/**
	 * When the request method is not set it will default to cli. This is 
	 * because php automatically sets the REQUEST_METHOD in the server super
	 * global for all http request but not for cli request. 
	 * 
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputFromDefaultsRequestMethodNotSet()
	{
		$uri = new RequestUri('my-route/af-param1/value1');
		$this->builder->setUri($uri);

		$this->assertSame(
			$this->builder,
			$this->builder->defineInputFromDefaults(),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	 => array('af-param1' => 'value1'),
			'post'	 => $_POST,
			'files'	 => $_FILES,
			'cookie' => $_COOKIE,
			'argv'	 => $_SERVER['argv']
		);

		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputNoParamsDontUseUri()
	{

		$emptyParams = array(
			'get'	 => array(),
			'post'	 => array(),
			'files'	 => array(),
			'cookie' => array(),
			'argv'	 => array()
		);

		$method = 'get';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, array(), false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($emptyParams, $input->getAll());	

		$method = 'post';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, array(), false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($emptyParams, $input->getAll());	

		$method = 'cli';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, array(), false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($emptyParams, $input->getAll());
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithGetParams()
	{
		$params = array(
			'get' => array('param1' => 'value1'),
		);
		$method = 'get';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	=> $params['get'],
			'post'  => array(),
			'files' => array(),
			'cookie' => array(),
			'argv'  => array()
		);
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithPostParams()
	{
		$params = array(
			'post' => array('param1' => 'value1'),
		);
		$method = 'post';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	=> array(),
			'post'  => $params['post'],
			'files' => array(),
			'cookie' => array(),
			'argv'  => array()
		);
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithFilesParams()
	{
		$this->assertNull($this->builder->getInput());

		$params = array(
			'files' => array('param1' => 'value1'),
		);
		$method = 'cli';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	=> array(),
			'post'  => array(),
			'files' => $params['files'],
			'cookie' => array(),
			'argv'  => array()
		);
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithCookieParams()
	{
		$this->assertNull($this->builder->getInput());

		$params = array(
			'cookie' => array('param1' => 'value1'),
		);
		$method = 'get';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	=> array(),
			'post'  => array(),
			'files' => array(),
			'cookie' => $params['cookie'],
			'argv'  => array()
		);
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithArgvParams()
	{
		$params = array(
			'argv' => array('param1' => 'value1'),
		);
		$method = 'cli';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$expected = array(
			'get'	=> array(),
			'post'  => array(),
			'files' => array(),
			'cookie' => array(),
			'argv'  => $params['argv']
		);
		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($expected, $input->getAll());	
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputAsWithAllParams()
	{
		$params = array(
			'get' => array('param1' => 'value1'),
			'post' => array('paramr2' => 'value2'),
			'files' => array('param3' => 'value3'),
			'cookie' => array('param4' => 'value4'),
			'argv' => array('param5' => 'value5'),
		);
		$method = 'cli';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInput($method, $params, false),
			'uses fluent interface'
		);
	
		$input = $this->builder->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);

		$this->assertEquals($method, $input->getMethod());
		$this->assertEquals($params, $input->getAll());	
	}

	/**
	 * When no configuration has been defined though the builders fluent 
	 * interface then it will look for a uri string in the sever super global
	 * $_SERVER['REQUEST_URI']
	 *
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testBuildWithNoConfiguration()
	{
		$route = 'my-route';
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$this->builder->setRouteKey($route)
					  ->setStrategy('ajax');
		$context = $this->builder->build();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContext',
			$context
		);
		
		$input = $context->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);
		$this->assertEquals('get', $input->getMethod());
		$expected = array('param1' => 'value1');
		$this->assertEquals($expected, $input->getAll('get'));
		$this->assertEquals($_POST, $input->getAll('post'));
		$this->assertEquals($_FILES, $input->getAll('files'));
		$this->assertEquals($_COOKIE, $input->getAll('cookie'));
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());
	}

	/**
	 *
	 * @dataProvider	provideValidContextStrategies
	 * @depends	testInterface
	 * @return	null
	 */
	public function estBuildWithUseUriString($strategy)
	{
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->useUriString('other-route/paramZ/valueY')
								 ->build();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContext',
			$context
		);
		
		$input = $context->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);
		$this->assertEquals('get', $input->getMethod());
		
		/* notice the params set to REQUEST_URI is ignored */
		$expected = array('paramZ' => 'valueY');
		$this->assertEquals($expected, $input->getAll('get'));
		$this->assertEquals($_POST, $input->getAll('post'));
		$this->assertEquals($_FILES, $input->getAll('files'));
		$this->assertEquals($_COOKIE, $input->getAll('cookie'));
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());

	}

	/**
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function estBuildWithUseSetUri($strategy)
	{
		$this->setupSuperGlobals('post', 'my-route/param1/value1');
		
		$uri = $this->getMock('Appfuel\Kernel\Mvc\RequestUriInterface');
		$params = array('paramXX' => 'valueYY');
		$uri->expects($this->once())
			->method('getParams')
			->will($this->returnValue($params));

		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->setUri($uri)
								 ->build();

		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContext',
			$context
		);
		
		$input = $context->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);
		$this->assertEquals('post', $input->getMethod());
		$this->assertEquals($params, $input->getAll('get'));
		$this->assertEquals($_POST, $input->getAll('post'));
		$this->assertEquals($_FILES, $input->getAll('files'));
		$this->assertEquals($_COOKIE, $input->getAll('cookie'));
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());
	}

	/**
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function estBuildWithSetInput($strategy)
	{
		$input = $this->getMock('Appfuel\Kernel\Mvc\AppInputInterface');

		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->setInput($input)
								 ->build();

		$this->assertSame($input, $context->getInput());
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());
	}


	/**
	 * This is the same as using build with no configurations
	 *
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function estBuildWithBuildInputFromDefaults($strategy)
	{
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->buildInputFromDefaults()
								 ->build();

		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContext',
			$context
		);
		
		$input = $context->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);
		$this->assertEquals('get', $input->getMethod());
		$expected = array('param1' => 'value1');
		$this->assertEquals($expected, $input->getAll('get'));
		$this->assertEquals($_POST, $input->getAll('post'));
		$this->assertEquals($_FILES, $input->getAll('files'));
		$this->assertEquals($_COOKIE, $input->getAll('cookie'));
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());
	}

	/**
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function estBuildWithDefineInputAs($strategy)
	{
		$params  = array(
			'get'		=> array('my-get'	 => 'value1'),
			'post'		=> array('my-post'	 => 'value2'),
			'files'		=> array('my-file'	 => 'value3'),
			'cookie'	=> array('my-cookie' => 'value4'),
			'argv'		=> array('my-argv'   => 'value5')
		);
		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->defineInputAs('cli', $params)
								 ->build();
		
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContextInterface',
			$context
		);
		
		$input = $context->getInput();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppInput',
			$input
		);
		$this->assertEquals('cli', $input->getMethod());
		$this->assertEquals($params['get'],		$input->getAll('get'));
		$this->assertEquals($params['post'],	$input->getAll('post'));
		$this->assertEquals($params['files'],	$input->getAll('files'));
		$this->assertEquals($params['cookie'],	$input->getAll('cookie'));
		$this->assertEquals($params['argv'],	$input->getAll('argv'));
		$this->assertEquals($strategy, $context->getStrategy());
		$this->assertEquals($route, $context->getRoute());
	}
}
