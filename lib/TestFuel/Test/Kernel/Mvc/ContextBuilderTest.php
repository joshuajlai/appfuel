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
		$uri = $this->builder->createRequestUri($uriString);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUri',
			$uri
		);
	}

	/**
	 * @depends	testGetSetUri
	 * @return	null
	 */
	public function xtestUseUriString()
	{
		$uriString = 'my-route/qx/param1/value1';
		$this->assertSame(
			$this->builder,
			$this->builder->useUriString($uriString),
			'uses fluent interface'
		);
		
		$uri = $this->builder->getUri();
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Context\ContextUriInterface',
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
}
