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
namespace TestFuel\Test\Kernel\Kernel;

use Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\AppContext,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\ContextBuilder,
	TestFuel\TestCase\ControllerTestCase;

/**
 * Test the ability for the builder to create request uri with its different
 * configurations (createRequestUri, setUri, useServerRequestUri, useUriString)
 * Also test the ability to create AppInput with its different configurations
 * (setInput, buildInputFromDefault, defineInputAs, createInput) Also test
 * the ability to set the error stack.
 */
class ContextBuilderTest extends ControllerTestCase
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
		$this->builder = new ContextBuilder();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
		$_SERVER = $this->serverBk;
		$this->builder = null;
    }

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\ContextBuilderInterface',
			$this->builder
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidContextStrategies()
	{
		return array(
			array('console'),
			array('ajax'),
			array('html')
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidContextStrategies()
	{
		return array(
			array('this-will-fail'),
			array('not-a-valid-strategy'),
			array('no-strategy-in-this-string')
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultStrategyIsNull()
	{
		$this->assertNull($this->builder->getStrategy());
	}

	/**
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function testGetSetStrategy($strategy)
	{
		$this->assertSame(
			$this->builder, 
			$this->builder->setStrategy($strategy)
		);
		$this->assertEquals($strategy, $this->builder->getStrategy());

		$newStrategy = strtoupper($strategy);
			$this->assertSame(
			$this->builder, 
			$this->builder->setStrategy($newStrategy)
		);
		$this->assertEquals($strategy, $this->builder->getStrategy());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetStrategy_Failure($strategy)
	{
		$this->builder->setStrategy($strategy);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidContextStrategies
	 * @depends				testInterface
	 * @return				null
	 */
	public function testSetStrategy_FailureStringNotCorrect($strategy)
	{
		$this->builder->setStrategy($strategy);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetUri()
	{
		$this->assertNull($this->builder->getUri(), 'default value is null');

		$uri = new RequestUri('my-route');
		$this->assertSame(
			$this->builder,
			$this->builder->setUri($uri),
			'uses fluent interface'
		);
		$this->assertSame($uri, $this->builder->getUri());	
	}

	/**
	 * @depends	testGetSetUri
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
	 * @depends	testInterface
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
	 * @depends	testGetSetUri
	 * @return	null
	 */
	public function testUseUriString()
	{
		$uriString = 'path/to/some/where?routekey=my-key';
		$this->assertSame(
			$this->builder,
			$this->builder->useUriString($uriString),
			'uses fluent interface'
		);
		
		$uri = $this->builder->getUri();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUri',
			$uri,
			'Uri object built from the uri string'
		);

		$this->assertEquals($uriString, $uri->getUriString());			
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetInput()
	{
		$this->assertNull($this->builder->getInput(), 'default value is null');
		
		$input = new AppInput('get');
		$this->assertSame(
			$this->builder,
			$this->builder->setInput($input),
			'uses fluent interface'
		);
		$this->assertSame($input, $this->builder->getInput());
	}

	/**
	 * @depends	testInterface
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
	public function testBuildInputFromDefaultsRequestMethodNotSet()
	{
		$this->assertNull($this->builder->getInput());

		$uri = new RequestUri('my-route/af-param1/value1');
		$this->builder->setUri($uri);

		$this->assertSame(
			$this->builder,
			$this->builder->buildInputFromDefaults(),
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
	public function testDefineInputAsNoParams()
	{
		$this->assertNull($this->builder->getInput());

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
			$this->builder->defineInputAs($method),
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
			$this->builder->defineInputAs($method),
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
			$this->builder->defineInputAs($method),
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
		$this->assertNull($this->builder->getInput());

		$params = array(
			'get' => array('param1' => 'value1'),
		);
		$method = 'get';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInputAs($method, $params),
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
		$this->assertNull($this->builder->getInput());

		$params = array(
			'post' => array('param1' => 'value1'),
		);
		$method = 'post';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInputAs($method, $params),
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
			$this->builder->defineInputAs($method, $params),
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
			$this->builder->defineInputAs($method, $params),
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
		$this->assertNull($this->builder->getInput());

		$params = array(
			'argv' => array('param1' => 'value1'),
		);
		$method = 'cli';
		$this->assertSame(
			$this->builder,
			$this->builder->defineInputAs($method, $params),
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
		$this->assertNull($this->builder->getInput());

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
			$this->builder->defineInputAs($method, $params),
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

	public function setupSuperGlobals($method, $uri)
	{
		$_SERVER['REQUEST_METHOD'] = $method;
		$_SERVER['REQUEST_URI'] = 'my-route/param1/value1';
		$_POST   = array('param2' => 'value 2');
		$_FILES  = array('param3' => 'value 3');
		$_COOKIE = array('param4' => 'value 4');
	
	}

	/**
	 * When no configuration has been defined though the builders fluent 
	 * interface then it will look for a uri string in the sever super global
	 * $_SERVER['REQUEST_URI']
	 *
	 * @dataProvider	provideValidContextStrategies
	 * @depends			testInterface
	 * @return			null
	 */
	public function testBuildWithNoConfiguration($strategy)
	{
		$route = 'my-route';
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$this->builder->setRoute($route)
					  ->setStrategy($strategy);
		$context = $this->builder->build();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContext',
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
	public function testBuildWithUseUriString($strategy)
	{
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->useUriString('other-route/paramZ/valueY')
								 ->build();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContext',
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
	public function testBuildWithUseSetUri($strategy)
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
			'Appfuel\Kernel\Mvc\AppContext',
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
	public function testBuildWithSetInput($strategy)
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
	public function testBuildWithBuildInputFromDefaults($strategy)
	{
		$this->setupSuperGlobals('get', 'my-route/param1/value1');
		$route   = 'my-route';
		$context = $this->builder->setRoute($route)
								 ->setStrategy($strategy)
								 ->buildInputFromDefaults()
								 ->build();

		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\AppContext',
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
	public function testBuildWithDefineInputAs($strategy)
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
			'Appfuel\Kernel\Mvc\AppContext',
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
