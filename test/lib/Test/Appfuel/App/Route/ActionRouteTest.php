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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
		$this->assertInstanceOf(
			'Appfuel\Framework\RouteInterface',
			$route,
			'route must implement the route interface'
		);
	
		$this->assertEquals($routeString, $route->getRouteString());
		$this->assertEquals($namespace, $route->getNamespace());
		$this->assertEquals($access, $route->getAccessPolicy());
		$this->assertEquals($returnType, $route->getReturnType());
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
    
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
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
		$returnType  = 'html';

		$route = new Route($routeString, $namespace, $access, $returnType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testReturnTypeEmpty()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = 'public';
		$returnType  = '';

		$route = new Route($routeString, $namespace, $access, $returnType);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testReturnTypeNonString()
    {
		$routeString = 'i/am/route';
		$namespace   = 'i_am_namespace';
		$access      = 'private';
		$returnType  = array(1,2,3);

		$route = new Route($routeString, $namespace, $access, $returnType);
    }
}

