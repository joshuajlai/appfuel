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
	public function testParamsRouteString()
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



}

