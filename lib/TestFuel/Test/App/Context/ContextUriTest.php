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

use StdClass,
	Appfuel\App\Context\ContextUri,
	TestFuel\TestCase\BaseTestCase;

/**
 * The uri is what decouples an operation the user requests from the action
 * the controller that executes it. The uri is designed to be a pretty uri 
 * meaning ? is not necessary to find the query string. However, the problem
 * this solution causes is knowing how to seperate the route string from the
 * rest of the query string. We solve this problem by assigning the first three
 * items in the path as the route and anything after that is the query string.
 * So a uri might look like this:
 *	something.com/my/route/string/param1/value1/param2/value2
 * 
 * Note: the Context uri can also handle ? so ?param=value will also get parsed
 *		 as a get parameter.
 */
class ContextUriTest extends BaseTestCase
{
	/**
	 * @return null
	 */
	public function testRootRoute()
	{
		$uriString = '/';
		$uri = new ContextUri($uriString);
	
		/* empty uri string always represents the root path */
		$this->assertEquals('/', $uri->getUriString());	
		$this->assertEquals('/', $uri->getRouteString());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}
	
	/**
	 * @depends	testRootRoute
	 * @return	null
	 */
	public function testParseToken()
	{
		$uriString = '/';
		$uri = new ContextUri($uriString);
	
		/* default parse token is 'pt' */
		$this->assertEquals('qx', $uri->getParseToken());

		$uri = new ContextUri($uriString, 'my-token');
		$this->assertEquals('my-token', $uri->getParseToken());
		
		/* must be a string even if the token is a number */
		$uri = new ContextUri($uriString, '123');
		$this->assertEquals('123', $uri->getParseToken());
	}

	/**
	 * The root path will yield the same results as an empty uri string
	 *
	 * @return null
	 */
	public function testEmptyRouteString()
	{
		$uriString = '';
		$uri = new ContextUri($uriString);
		
		/* empty uri string always represents the root path */
		$this->assertEquals('/', $uri->getUriString());	
		$this->assertEquals('/', $uri->getRouteString());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}

	/**
	 * The normal use case is to provide a parse token in the uri which 
	 * tells the framework where the route ends and where the parameters 
	 * begin
	 *
	 * @reutrn	null
	 */
	public function testRouteWithParseToken()
	{
		$uriString = 'myroute/qx/param1/value1/param2/value2/param3/value3';
		$uri = new ContextUri($uriString);
		
		$expectedParams = array(
			'param1' => 'value1',
			'param2' => 'value2',
			'param3' => 'value3',
		);
		$expectedString = 'param1/value1/param2/value2/param3/value3';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteLooksLikePathWithParseToken()
	{
		$uriString = 'my/other/route/qx/param1/value1/param2/value2';
		$uri = new ContextUri($uriString);
		
		$expectedParams = array(
			'param1' => 'value1',
			'param2' => 'value2',
		);
		$expectedString = 'param1/value1/param2/value2';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my/other/route', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteNoParams()
	{
		$uriString = 'myroute';
		$uri = new ContextUri($uriString);
		
		$expectedParams = array();
		$expectedString = '';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteNoParamsWithTokenOneSlash()
	{
		$uriString = 'myroute/qx';
		$uri = new ContextUri($uriString);
		$expectedParams = array();
		$expectedString = '';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteNoParamsWithTokenBothSlashes()
	{
		$uriString = 'myroute/qx/';
		$uri = new ContextUri($uriString);
		$expectedParams = array();
		$expectedString = '';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteNoParamsQueryStringWithParams()
	{
		$uriString = 'myroute?param1=value1&param2=value2';
		$uri = new ContextUri($uriString);
		$expectedParams = array(
			'param1' => 'value1',
			'param2' => 'value2',
		);

		$expectedString = 'param1/value1/param2/value2';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @reutrn	null
	 */
	public function testRouteWithParamsQueryStringWithParams()
	{
		$uriString = 'myroute/qx/param1/value1?param2=value2&param3=value3';
		$uri = new ContextUri($uriString);
		$expectedParams = array(
			'param2' => 'value2',
			'param3' => 'value3',
			'param1' => 'value1',
			
		);

		$expectedString = 'param1/value1/param2/value2/param3/value3';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('myroute', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * When the route is empty it is automatically assigned to the root route
	 * as '/'
	 * 
	 * @reutrn	null
	 */
	public function testNoRouteWithQueryStringWithParams()
	{
		$uriString = '?param2=value2&param3=value3';
		$uri = new ContextUri($uriString);
		$expectedParams = array(
			'param2' => 'value2',
			'param3' => 'value3',
			
		);

		$expectedString = 'param2/value2/param3/value3';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('/', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * The default parse token is qx so we will use that. When only the parse
	 * token is available then the whole string is treated as a route. This is
	 * an error condition probably from automated build of the uri
	 * 
	 * @reutrn	null
	 */
	public function testRouteAsTheParseToken()
	{
		$uriString = '/qx/param1/value1';
		$uri = new ContextUri($uriString);
		$expectedParams = array();

		$expectedString = '';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals(
			trim($uriString, "' ','/' "), 
			$uri->getRouteString()
		);

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expectedString, $uri->getParamString()); 
	}

	/**
	 * @return	null
	 */
	public function testRouteSameAsParseTokenWithParams()
	{
		$uriString = 'my-token/my-token/param1/value1';
		$uri = new ContextUri($uriString, 'my-token');

		$expectedParams = array(
			'param1' => 'value1'
		);
		$expected = 'param1/value1';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my-token', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expected, $uri->getParamString()); 
	}

	/**
	 * @return	null
	 */
	public function testRouteSameAsParseTokenWithParamsWithForwardSlash()
	{
		$uriString = '/my-token/my-token/param1/value1';
		$uri = new ContextUri($uriString, 'my-token');
		
		$expectedParams = array(
			'param1' => 'value1'
		);
		$expected = 'param1/value1';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my-token', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expected, $uri->getParamString()); 

	}

	/**
	 * @return	null
	 */
	public function testRouteParamHasNoValue()
	{
		$uriString = 'my-route/qx/param1/';
		$uri = new ContextUri($uriString);
		$expectedParams = array(
			'param1' => null
		);
		$expected = 'param1';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my-route', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expected, $uri->getParamString()); 

		$uriString = 'my-route/qx/param1';
		$uri = new ContextUri($uriString);
		
		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my-route', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expected, $uri->getParamString());
	}

	/**
	 * @return	null
	 */
	public function testRouteParamHasNoValueOtherParamsDo()
	{
		$uriString = 'my-route/qx/param1//param2/value2';
		$uri = new ContextUri($uriString);
		$expectedParams = array(
			'param1' => null,
			'param2' => 'value2'
		);
		$expected = 'param1//param2/value2';

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('my-route', $uri->getRouteString());

		$this->assertEquals($expectedParams, $uri->getParams());
		$this->assertEquals($expected, $uri->getParamString()); 
	}
}
