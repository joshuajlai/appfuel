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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\App\Route\ActionRoute as Route;

/**
 * Test the value object to ensure the members are immutable
 */
class ActionRouteTest extends BaseTestCase
{
    /**
     * @return null
     */
    public function testMembers()
    {
		$routeString = 'i/am/a/route/string';
		$namespace   = 'Appfuel\App\Action\Error\Handler\Invalid';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Route\RouteInterface',
			$route,
			'route must implement the route interface'
		);
	
		$this->assertEquals($routeString, $route->getRouteString());
		$this->assertEquals($namespace, $route->getActionNamespace());
		$this->assertEquals($access, $route->getAccessPolicy());
		$this->assertEquals($responseType, $route->getResponseType());

		/* test sub module namespace was parsed correctly */
		$expected = 'Appfuel\App\Action\Error\Handler';
		$this->assertEquals($expected, $route->getSubModuleNamespace());

		/* test module namespace was parsed correctly */
		$expected = 'Appfuel\App\Action\Error';
		$this->assertEquals($expected, $route->getModuleNamespace());

		/* test root namespace for actions was parsed correctly */
		$expected = 'Appfuel\App\Action';
		$this->assertEquals($expected, $route->getRootActionNamespace());
    }

    /**
	 * Failure should occur when after parsing the action namespace a sub module
	 * level namespace is not found
	 *
	 * the action namespace should have the following form :
	 * root_level_ns\module_ns\submodule_ns\action_ns
	 *
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
	public function testInvalidSubModule()
	{
		$routeString = 'i/am/a/route/string';
		$namespace   = 'Error';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
	}

    /**
	 * Failure should occur when after parsing the action namespace a module
	 * level namespace is not found
	 *
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
	public function testInvalidModule()
	{
		$routeString = 'i/am/a/route/string';
		$namespace   = 'Error\Handler';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
	}

    /**
	 * Failure should occur when after parsing the action namespace a root
	 * level namespace is not found
	 *
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
	public function testInvalidRootNamespace()
	{
		$routeString = 'i/am/a/route/string';
		$namespace   = 'Error\Handler\Invalid';
		$access      = 'public';
		$responseType  = 'html';

		$route = new Route($routeString, $namespace, $access, $responseType);
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

