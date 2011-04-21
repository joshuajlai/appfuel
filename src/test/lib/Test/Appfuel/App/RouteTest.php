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
	Appfuel\App\Route,
	StdClass;

/**
 * Test the value object to ensure the members are immutable
 */
class RouteTest extends ParentTestCase
{
    /**
     * System under test
     * @var Route
     */
    protected $route = null;

    /**
     * @return null
     */
    public function testMembers()
    {
		$routeString = 'i/am/a/route/string';
		$class       = 'IAmControllerClass';
		$route = new Route($routeString, $class);
		
		$this->assertEquals($routeString, $route->getRouteString());
		$this->assertEquals($class, $route->getControllerClass());
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testEmptyRouteString()
    {
		$routeString = '';
		$class       = 'IAmControllerClass';
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testArrayRouteString()
    {
		$routeString = array(1,2,3);
		$class       = 'IAmControllerClass';
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testObjRouteString()
    {
		$routeString = new StdClass();
		$class       = 'IAmControllerClass';
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testIntRouteString()
    {
		$routeString = 99;
		$class       = 'IAmControllerClass';
		$route = new Route($routeString, $class);
    }


    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testEmptyControllerClass()
    {
		$routeString = 'i/am/a/route/string';
		$class       = '';
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testArrayControllerClass()
    {
		$routeString = 'i/am/a/route/string';
		$class       = array(1,2,3);
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testObjControllerClass()
    {
		$routeString = 'i/am/a/route/string';
		$class       = new StdClass();
		$route = new Route($routeString, $class);
    }

    /**
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
     */
    public function testIntControllerClass()
    {
		$routeString = 'i/am/a/route/string';
		$class       = 99;
		$route = new Route($routeString, $class);
    }




}

