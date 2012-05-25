<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Appfuel\Kernel\Mvc\RequestUri,
	TestFuel\TestCase\BaseTestCase;

/**
 * The request uri is composed of the path and query string. Even though 
 * the commandline does not use http get, the same uri is generated for
 * commandline requests. This uri class can handle pretty urls, we will
 * test that in pretty urls the first part of the path is the route key.
 * This uri class can handle regular urls where the route key is in the
 * query string labeled as 'routekey', we will test that this is parsed
 * correctly. Finally this uri class can parse a hybrid uri where the
 * route key is in the query string or in the path, but when both one
 * gets ignored.
 */
class RequestUriTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	RequestUri
	 */
	protected $uri = null;

	/**
	 * The first and only parameter in the constructor
	 * @var string
	 */
	protected $uristring = null;

	/**
	 * The route key which will be the first part of the path in the uristring
	 * @var string
	 */
	protected $routekey = null;
	
	/**
	 * list of params produced by the uri
	 * @var array
	 */
	protected $params = array();

	/**
	 * Parameter string produced by the uri in setup
	 * @var string
	 */
	protected $paramstring = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->routekey  = 'my-route-key';
		$this->uristring = "{$this->routekey}/param1/value1/param2/value2";
		$this->params    = array('param1' => 'value1', 'param2' => 'value2');
		$this->paramstring = 'param1/value1/param2/value2';

		$this->uri		 = new RequestUri($this->uristring);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->uri = null;
	}

	/**
	 * @return	array
	 */
	public function provideEmptyUri()
	{
		return array(
			array(''), array(' '), array('  '), array("\n"), array(" \n"),
			array(" \t"), array(" \n\t"), array("/"), array(" /"), array("/ "),
			array(" / "), array("\n/"), array("\n\t/"), array(" \n\t / "),
			array("%20"), array("%20\t /"), array(" / %20 /")
		);
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RequestUriInterface',
			$this->uri
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testNoQueryString()
	{
		$this->assertEquals($this->routekey, $this->uri->getRouteKey());
		$this->assertEquals($this->params, $this->uri->getParams());
		$this->assertEquals($this->paramstring, $this->uri->getParamString());
		$this->assertEquals($this->uristring, $this->uri->getUriString());	
	}

	/**
	 * @depends			testInterface
	 * @dataProvider	provideEmptyUri
	 * @return			null
	 */
	public function testUriEmpty($uri)
	{
		$uri = new RequestUri($uri);
		
		$this->assertEquals('', $uri->getRouteKey());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testQueryStringWithPath()
	{
		$uristring = 'param1/value1?routekey=my-key&param2=value2';
		$uri	   = new RequestUri($uristring);
		$expected  = array('param1' => 'value1', 'param2' => 'value2');
		
		$this->assertEquals('my-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals(
			"param1/value1/param2/value2",
			$uri->getParamString()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testQueryStringNoPath()
	{
		$uristring = '?routekey=my-key&param1=value1';
		$uri	   = new RequestUri($uristring);
		$expected  = array('param1' => 'value1');
		
		$this->assertEquals('my-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals("param1/value1",$uri->getParamString());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testQueryStringNoPathNoRoute()
	{
		$uristring = '?param1=value1';
		$uri	   = new RequestUri($uristring);
		$expected  = array('param1' => 'value1');
		
		$this->assertEquals('', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals("param1/value1",$uri->getParamString());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testQueryStringSymbol()
	{
		$uristring = '?';
		$uri	   = new RequestUri($uristring);
		$this->assertEquals('', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals("",$uri->getParamString());
	}

	/**	
	 * The key value pair is ignored is the key is empty
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testPathEmptyParam()
	{
		$uristring = 'route-key/param1/value1//value2';
		$uri	   = new RequestUri($uristring);

		$this->assertEquals('route-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals(array('param1'=>'value1'), $uri->getParams());
		$this->assertEquals("param1/value1",$uri->getParamString());

		$uristring = 'route-key/param1/value1/%20/value2';
		$uri	   = new RequestUri($uristring);
		$this->assertEquals('route-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals(array('param1'=>'value1'), $uri->getParams());
		$this->assertEquals("param1/value1",$uri->getParamString());
	}

	/**	
	 * The key value pair is ignored is the key is empty
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testPathEmptyValue()
	{
		$uristring = 'route-key/p1/value1/p2//p3/my-value';
		$uri	   = new RequestUri($uristring);
		$expected  = array(
			'p1' => 'value1',
			'p2' => null,
			'p3' => 'my-value'
		);
		$this->assertEquals('route-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals("p1/value1/p2//p3/my-value",$uri->getParamString());
	}

	/**	
	 * The key value pair is ignored is the key is empty
	 * 
	 * @depends	testInterface
	 * @return	null
	 */
	public function testPathEmptyValueAtEnd()
	{
		$uristring = 'route-key/p1/value1/p2/';
		$uri	   = new RequestUri($uristring);
		$expected  = array(
			'p1' => 'value1',
			'p2' => null,
		);
		$this->assertEquals('route-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals("p1/value1/p2",$uri->getParamString());
		
		$uristring = 'route-key/p1/value1/p2';
		$uri	   = new RequestUri($uristring);
		$this->assertEquals('route-key', $uri->getRouteKey());
		$this->assertEquals($uristring, $uri->getUriString());
		$this->assertEquals($expected, $uri->getParams());
		$this->assertEquals("p1/value1/p2",$uri->getParamString());
		
	}
}
