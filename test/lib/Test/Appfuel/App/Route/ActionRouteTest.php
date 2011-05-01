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
	Appfuel\App\Route\ActionRoute as Route,
	StdClass;

/**
 * Test the value object to ensure the members are immutable
 */
class ActionRouteTest extends ParentTestCase
{
    /**
     * @return null
     */
    public function testMembers()
    {
		$routeString = 'i/am/a/route/string';
		$namespace   = 'i_am_a_namespace';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
		$this->assertInstanceOf(
			'Appfuel\Framework\RouteInterface',
			$route,
			'route must implement the route interface'
		);
	
		$this->assertEquals($routeString, $route->getRouteString());
		$this->assertEquals($namespace, $route->getNamespace());
		$this->assertEquals($access, $route->getAccessPolicy());
		$this->assertEquals($responseType, $route->getResponseType());
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testEmptyRouteString()
    {
		$routeString = '';
		$namespace   = 'i_am_a_namespace';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    
	}
    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testNonStringRoute()
    {
		$routeString = 99;
		$namespace   = 'i_am_a_namespace';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testEmptyNamespace()
    {
		$routeString = 'i/am/route';
		$namespace   = '';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testNamespaceNonString()
    {
		$routeString = 'i/am/route';
		$namespace   = 99;
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testAccessEmpty()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = '';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testAccessNonString()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = array(1,2,3);
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testResponseTypeEmpty()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = 'public';
		$responseType  = '';

		$route = new Route($routeString, $namespace, $access, $responseType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testResponseTypeNonString()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = 'private';
		$responseType  = array(1,2,3);

		$route = new Route($routeString, $namespace, $access, $responseType);
    }
}

