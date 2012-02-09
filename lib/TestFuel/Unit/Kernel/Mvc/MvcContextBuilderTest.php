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
	public function testDefineInputWithGetParams()
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
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputFromDefaultsRequestMethodNotDefined()
	{
		$_SERVER['REQUEST_METHOD'] = null;
		$this->assertSame(
			$this->builder,
			$this->builder->defineInputFromDefaults(false)
		);
		$input = $this->builder->getInput();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('cli', $input->getMethod());
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineInputFromDefaultsRequestMethodNotString()
	{
		$_SERVER['REQUEST_METHOD'] = array(1,2,3);
		$this->assertSame(
			$this->builder,
			$this->builder->defineInputFromDefaults(false)
		);
		$input = $this->builder->getInput();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('cli', $input->getMethod());
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testDefineUriForInputSource()
	{
		$_SERVER['QUERY_STRING'] = 'routekey=my-route&param1=value1';
		$this->assertSame(
			$this->builder,
			$this->builder->defineUriForInputSource()
		);

		$input = $this->builder->getInput();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals('value1', $input->get('get', 'param1'));
	}

	/**
	 * @depends	testCreateInput
	 * @return	null
	 */
	public function testNoInputRequired()
	{
		$this->assertSame(
			$this->builder,
			$this->builder->noInputRequired()
		);		
		$input = $this->builder->getInput();
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('get', $input->getMethod());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddAclCode()
	{
		$code1 = 'my-admin';
		$code2 = 'your-admin';
		$code3 = 'our-admin';
			
		$this->assertSame($this->builder, $this->builder->addAclCode($code1));
		
		$expected = array($code1);
		$this->assertEquals($expected, $this->builder->getAclCodes());
		
		$this->assertSame($this->builder, $this->builder->addAclCode($code2));
		
		$expected[] = $code2;
		$this->assertEquals($expected, $this->builder->getAclCodes());
			
		$this->assertSame($this->builder, $this->builder->addAclCode($code3));
		
		$expected[] = $code3;
		$this->assertEquals($expected, $this->builder->getAclCodes());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testLoadAclCodesWhenEmpty()
	{
		$list = array('my-admin', 'your-admin', 'our-admin');
		$this->assertEquals(array(), $this->builder->getAclCodes());
		$this->assertSame($this->builder, $this->builder->loadAclCodes($list));
		$this->assertEquals($list, $this->builder->getAclCodes());
	}

	/**
	 * @depends testLoadAclCodesWhenEmpty	
	 * @return	null
	 */
	public function testLoadAclCodesWhenNotEmpty()
	{
		$list1 = array('my-admin', 'your-admin', 'our-admin');
		$list2 = array('guest', 'staff', 'publisher');
		$this->assertSame($this->builder, $this->builder->loadAclCodes($list1));
		$this->assertSame($this->builder, $this->builder->loadAclCodes($list2));

		$expected = array_merge($list1, $list2);
		$this->assertSame($expected, $this->builder->getAclCodes());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetAclCodesWhenEmpty()
	{
		$list = array('my-admin', 'your-admin', 'our-admin');
		$this->assertEquals(array(), $this->builder->getAclCodes());
		$this->assertSame($this->builder, $this->builder->setAclCodes($list));
		$this->assertEquals($list, $this->builder->getAclCodes());
	}

	/**
	 * @depends testLoadAclCodesWhenEmpty	
	 * @return	null
	 */
	public function testSetAclCodesWhenNotEmpty()
	{
		$list1 = array('my-admin', 'your-admin', 'our-admin');
		$list2 = array('guest', 'staff', 'publisher');
		$this->assertSame($this->builder, $this->builder->setAclCodes($list1));
		$this->assertSame($this->builder, $this->builder->setAclCodes($list2));

		$this->assertSame($list2, $this->builder->getAclCodes());
	}

	/**
	 * @dataProvider	provideAllStringsIncludingCastable
	 * @depends			testInitialState
	 * @return			null
	 */
	public function testSetViewCastableStrings($str)
	{
		$this->assertSame($this->builder, $this->builder->setView($str));
		$this->assertEquals((string)$str, $this->builder->getView());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNoCastableStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetViewNotCastableStrings($str)
	{
		$this->builder->setView($str);
	}
}
