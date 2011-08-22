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
namespace TestFuel\Test\App;

use Appfuel\App\Request,
	TestFuel\TestCase\BaseTestCase;

/**
 * The request object was designed to service web,api and cli request
 */
class RequestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Request
	 */
	protected $request = null;

	/**
	 * used to backup super globals so they can be restored
	 * @var array
	 */
	protected $backup = array();

	/**
	 * Backup super global so we can use them to inject data into the request
	 * 
	 * @return null
	 */
	public function setUp()
	{
		$argv = array();
		if (array_key_exists('argv', $_SERVER)) {
			$argv = $_SERVER['argv'];
		}

		$method = null;
		if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
			$method = $_SERVER['REQUEST_METHOD'];
		}

		$this->backup = array(
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE,
			'argv'   => $argv,
			'method' => $method
		);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		$_POST   = $this->backup['post'];
		$_FILES  = $this->backup['files'];
		$_COOKIE = $this->backup['cookie'];

		$_SERVER['argv'] = $this->backup['argv'];
		$_SERVER['REQUEST_METHOD'] = $this->backup['method'];
	}

	/**
	 * Test creating a request when no request method, or argv is in the 
	 * server super global
	 *
	 * @return null
	 */
	public function testConstructAllDefaults()
	{
		if (array_key_exists('argv', $_SERVER)) {
			unset($_SERVER['argv']);
		}

		if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
			unset($_SERVER['REQUEST_METHOD']);
		}

		$uriString = 'some/route';
		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		
		$uri->expects($this->any())
			->method('getUriString')
			->will($this->returnValue($uriString));

		$path = 'my/path';
		$uri->expects($this->any())
			->method('getPath')
			->will($this->returnValue($path));

		$paramString = 'param/string';
		$uri->expects($this->any())
			->method('getParamString')
			->will($this->returnValue($paramString));

	
		$params = array();
		$uri->expects($this->any())
			->method('getParams')
			->will($this->returnValue($params));

		$request = new Request($uri);
		
		/* the default method is GET */
		$this->assertFalse($request->isPost());
		$this->assertTrue($request->isGet());
		$this->assertEquals('get', $request->getMethod());

		/* mocked returns */
		$this->assertEquals($uriString, $request->getUriString());
		$this->assertEquals($path, $request->getRouteString());
		$this->assertEquals($paramString, $request->getParamString());

		/* get all data which will be an empty array */
		$this->assertEquals(array(), $request->getAll('get'));
		$this->assertEquals(array(), $request->getAll('post'));
		$this->assertEquals(array(), $request->getAll('files'));
		$this->assertEquals(array(), $request->getAll('cookie'));
		$this->assertEquals(array(), $request->getAll('argv'));
	}

	/**
	 * getMethod reports what was in the $_SERVER['REQUEST_METHOD'] when
	 * the constructor was called
	 *
	 * @return null
	 */
	public function testGetMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'post';
		
		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$request = new Request($uri);
		$this->assertTrue($request->isPost());
		$this->assertFalse($request->isGet());
		$this->assertEquals('post', $request->getMethod());

		/* switch to GET now */
		$_SERVER['REQUEST_METHOD'] = 'get';
		$request = new Request($uri);
		$this->assertTrue($request->isGet());
		$this->assertFalse($request->isPost());
		$this->assertEquals('get', $request->getMethod());

		/* prove this is case insensitive */
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$request = new Request($uri);
		$this->assertTrue($request->isPost());
		$this->assertFalse($request->isGet());
		$this->assertEquals('post', $request->getMethod());
	}

	/**
	 * The request object obtains its GET parameters from the uri interface
	 * getParams. We will be testing that here
	 *
	 * @return null
	 */
	public function testGetParams()
	{
		$params = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3'
		);
		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$uri->expects($this->any())
			->method('getParams')
			->will($this->returnValue($params));

		$request = new Request($uri);
		$this->assertEquals($params, $request->getAll('get'));

		foreach ($params as $param => $value) {
			$this->assertEquals($value, $request->get($param, 'get'));
		}

		/* test get when no parameter exists */
		$default = 'my_default_value';
		$this->assertEquals($default, $request->get('blah', 'get', $default));

		/* default value for the default parameter of get is null */
		$this->assertNull($request->get('blah', 'get'));
	}

	/**
	 * The request object obtains its post parameters from the super global
	 * $_POST
	 *
	 * @return null
	 */
	public function testPostParams()
	{
		$_POST = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3'
		);		
		$params = $_POST;

		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$request = new Request($uri);
		$this->assertEquals($params, $request->getAll('post'));

		foreach ($params as $param => $value) {
			$this->assertEquals($value, $request->get($param, 'post'));
		}

		/* test get when no parameter exists */
		$default = 'my_default_value';
		$this->assertEquals($default, $request->get('blah', 'post', $default));

		/* default value for the default parameter of get is null */
		$this->assertNull($request->get('blah', 'post'));
	}

	/**
	 * The request object obtains its cookie parameters from the super global
	 * $_COOKIE
	 *
	 * @return null
	 */
	public function testCookieParams()
	{
		$_COOKIE = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3'
		);		
		$params = $_COOKIE;

		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$request = new Request($uri);
		$this->assertEquals($params, $request->getAll('cookie'));

		foreach ($params as $param => $value) {
			$this->assertEquals($value, $request->get($param, 'cookie'));
		}

		/* test get when no parameter exists */
		$default = 'my_default_value';
		$this->assertEquals($default, $request->get('not', 'cookie', $default));

		/* default value for the default parameter of get is null */
		$this->assertNull($request->get('blah', 'cookie'));
	}

	/**
	 * The request object obtains its files parameters from the super global
	 * $_FILES
	 *
	 * @return null
	 */
	public function testFilesParams()
	{
		$_FILES = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3'
		);		
		$params = $_FILES;

		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$request = new Request($uri);
		$this->assertEquals($params, $request->getAll('files'));

		foreach ($params as $param => $value) {
			$this->assertEquals($value, $request->get($param, 'files'));
		}

		/* test get when no parameter exists */
		$default = 'my_default_value';
		$this->assertEquals($default, $request->get('not', 'files', $default));

		/* default value for the default parameter of get is null */
		$this->assertNull($request->get('blah', 'files'));
	}

	/**
	 * The request object obtains its argv parameters from the super global
	 * $_SERVER
	 *
	 * @return null
	 */
	public function testArgvParams()
	{
		$_SERVER['argv'] = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3'
		);		
		$params = $_SERVER['argv'];

		$uri = $this->getMock('Appfuel\Framework\App\Request\UriInterface');
		$request = new Request($uri);
		$this->assertEquals($params, $request->getAll('argv'));

		foreach ($params as $param => $value) {
			$this->assertEquals($value, $request->get($param, 'argv'));
		}

		/* test get when no parameter exists */
		$default = 'my_default_value';
		$this->assertEquals($default, $request->get('not', 'argv', $default));

		/* default value for the default parameter of get is null */
		$this->assertNull($request->get('blah', 'argv'));
	}
}
