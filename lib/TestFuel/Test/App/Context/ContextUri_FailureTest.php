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
 * Test the failures that throw exceptions
 */
class ContextUri_FailureTest extends BaseTestCase
{

	/**
	 * You can not have a route string thats the same as the parse token when
	 * the route string is the only text of the uri
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testRouteSameAsParseToken()
	{
		$uriString = 'my-token';
		$uri = new ContextUri($uriString, $uriString);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteParseTokenEmptyString()
	{
		$uriString = 'my-route//param1/value1';
		$uri = new ContextUri($uriString, '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteParseTokenArray()
	{
		$uriString = 'my-route//param1/value1';
		$uri = new ContextUri($uriString, array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteParseTokenObject()
	{
		$uriString = 'my-route//param1/value1';
		$uri = new ContextUri($uriString, new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testUriStringIsInt()
	{
		$uri = new ContextUri(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testUriStringIsArray()
	{
		$uri = new ContextUri(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testUriStringIsObject()
	{
		$uri = new ContextUri(new StdClass());
	}
}
