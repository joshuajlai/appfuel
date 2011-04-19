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
namespace Test\Appfuel\App;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\PrettyUri;

/**
 * Currenty the only supporting uri system is the pretty uri. The pretty uri
 * works on the premis that uri path and uri parameters look exactly the same.
 * For example http://www.somedomain.com/path/info/param1/value1/param2/value2
 * is a pretty url. The path path/info is what appfuel calls the route string.
 * The route string is restricted a min of 1 to a max of 3 paths. 
 */
class PrettyUriTest extends ParentTestCase
{
	/**
	 * @return null
	 */
	public function testEmptyPathRouteString()
	{
		$uriString = '';
		$uri = new PrettyUri($uriString);
		
		/* empty uri string always represents the root path */
		$this->assertEquals('/', $uri->getUriString());	
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}

	/**
	 * The root path will yield the same results as an empty uri string
	 *
	 * @return null
	 */
	public function testRootPathRouteString()
	{
		$uriString = '/';
		$uri = new PrettyUri($uriString);
		
		/* empty uri string always represents the root path */
		$this->assertEquals('/', $uri->getUriString());	
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}

	/**
	 * @return null
	 */
	public function testOnePathRouteString()
	{
		$uriString = '/one';
		$uri = new PrettyUri($uriString);
		
		$this->assertEquals('/one', $uri->getUriString());	
		$this->assertEquals('one', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 

		/* same string with out the slash in the beginning */
		$uriString = 'one';
		$uri = new PrettyUri($uriString);
		
		$this->assertEquals('one', $uri->getUriString());	
		$this->assertEquals('one', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}

	/**
	 * @return null
	 */
	public function testTwoPathRouteString()
	{
		$uriString = '/one/two';
		$uri = new PrettyUri($uriString);

		$this->assertEquals('/one/two', $uri->getUriString());	
		$this->assertEquals('one/two', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 

		/* same string with out the slash in the beginning */
		$uriString = 'one/two';
		$uri = new PrettyUri($uriString);
		
		$this->assertEquals('one/two', $uri->getUriString());	
		$this->assertEquals('one/two', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}

	/**
	 * @return null
	 */
	public function testThreePathRouteString()
	{
		$uriString = '/one/two/three';
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 

		/* same string with out the slash in the beginning */
		$uriString = 'one/two/three';
		$uri = new PrettyUri($uriString);
		
		/* the uri string is exactly what you give it, not altered */	
		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals($uriString, $uri->getPath());
		$this->assertEquals(array(), $uri->getParams());
		$this->assertEquals('', $uri->getParamString()); 
	}


	/**
	 * The path can have only 3 parts to it. After that everything else are
	 * considered GET parameters
	 *
	 * @return null
	 */
	public function testParamsSimpleRouteString()
	{
		$uriString = '/one/two/three/param1/value1';
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(1, count($params));
		$this->assertEquals(array('param1'=>'value1'), $params);

		$this->assertEquals('param1/value1', $uri->getParamString());

	}


	/**
	 * @return null
	 */
	public function testParamsLongRouteString()
	{
		$params = array(
			'param1' => 'value_1',
			'param2' => '2',
			'param3' => 'value_3',
			'param4' => 'value_4'
		);

		/* make a param string and remove the trailing slash when done */
		$paramString = '';
		foreach ($params as $key => $value) {
			$paramString .= "$key/$value/";
		}
		$paramString = trim($paramString, "/");

		$uriString = "/one/two/three/$paramString";
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$result = $uri->getParams();
		$this->assertInternalType('array', $result);
		$this->assertEquals(4, count($result));
		$this->assertEquals($params, $result);

		$this->assertEquals($paramString, $uri->getParamString());
	}

	public function testParamQuestionMarkStyle()
	{
		$uriString = "/one/two/three?param1=value1";
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(1, count($params));
		$this->assertEquals(array('param1'=>'value1'), $params);

		$this->assertEquals('param1/value1', $uri->getParamString());
	}

	public function testParamQuestionMarkStyleMany()
	{
		$params = array(
			'param1' => 'value_1',
			'param2' => '2',
			'param3' => 'value_3',
			'param4' => 'value_4'
		);

		/* make a param string and remove the trailing slash when done */
		$paramString = '';
		foreach ($params as $key => $value) {
			$paramString .= "$key=$value&";
		}
		$paramString = trim($paramString, "&");
		
		$uriString = "/one/two/three?$paramString";
		$uri = new PrettyUri($uriString);
		
		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$result = $uri->getParams();
		$this->assertInternalType('array', $result);
		$this->assertEquals(4, count($result));
		$this->assertEquals($params, $result);

		/* the parameter string will always be returned as pretty */
		$paramString = '';
		foreach ($params as $key => $value) {
			$paramString .= "$key/$value/";
		}
		$paramString = trim($paramString, "/");
			
		$this->assertEquals($paramString, $uri->getParamString());
	}

	/**
	 * The pretty uri must always have key/value pairs if a pair can not
	 * be matched up one then it is ignored
	 *
	 * @return null
	 */
	public function testParamMissingValueForKey()
	{
		$uriString = '/one/two/three/param1/';
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(1, count($params));
		$this->assertEquals(array('param1' => null), $params);

		$this->assertEquals('param1', $uri->getParamString());

		/* test when there are more one or more good params with an odd one */
		$uriString = '/one/two/three/param1/value1/param2';
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(1, count($params));
		$this->assertEquals(array('param1' => 'value1'), $params);

		$this->assertEquals('param1/value1/param2', $uri->getParamString());
	}

	/**
	 * Odd parameters when params are declared using ?
	 *
	 * @return null
	 */
	public function testParamMissingQuestionMarkStyle()
	{
		$uriString = "/one/two/three?param1=value1&param2";
		$uri = new PrettyUri($uriString);

		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(1, count($params));
		$this->assertEquals(array('param1'=>'value1'), $params);

		$this->assertEquals('param1/value1', $uri->getParamString());
	}

	/**
	 * Empty parameters when params are declared using ? the
	 * param would look like param1=
	 *
	 * @return null
	 */
	public function testParamEmptyQuestionMarkStyle()
	{
		$uriString = "/one/two/three?param1=value1&param2=";
		$uri = new PrettyUri($uriString);
		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(2, count($params));

		$expected = array(
			'param1' => 'value1',
			'param2' => null,
		);

		$this->assertEquals($expected, $params);

		/* 
		 * note the trailing slash because we are converting to pretty url
		 * which can not account for an empty value
		 */
		$this->assertEquals('param1/value1/param2/', $uri->getParamString());
	}

	/**
	 * Empty parameters with pretty uri
	 *
	 * @return null
	 */
	public function testParamEmptyPrettyStyle()
	{
		$uriString = "/one/two/three/param1//param2/value2";
		$uri = new PrettyUri($uriString);
		
		$this->assertEquals($uriString, $uri->getUriString());	
		$this->assertEquals('one/two/three', $uri->getPath());

		$params = $uri->getParams();
		$this->assertInternalType('array', $params);
		$this->assertEquals(2, count($params));

		$expected = array(
			'param1' => null,
			'param2' => 'value2',
		);

		$this->assertEquals($expected, $params);

		/* 
		 * note the trailing slash because we are converting to pretty url
		 * which can not account for an empty value
		 */
		$this->assertEquals('param1//param2/value2', $uri->getParamString());
	}

	/**
	 *
	 */
	public function testSingleEmptyParamPretty()
	{

	}


}

