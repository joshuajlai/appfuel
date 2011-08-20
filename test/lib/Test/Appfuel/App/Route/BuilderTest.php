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
namespace Test\Appfuel\App\Route;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Route\ActionRoute as Route,
	Appfuel\App\Route\Builder;

/**
 * The route builder's primary responsibility is to locate the route string
 * in the routes ini and turn the string it finds into an ActionRoute object
 */
class BuilderTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var string
	 */
	protected $builder = null;

	/**
	 * Path to file used to simulate the routes.ini
	 * @var string
	 */
	protected $filePath = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$path = 'files' . DIRECTORY_SEPARATOR . 'routes.ini';
		$this->filePath = $this->getCurrentPath($path);
		$this->builder  = new Builder($this->filePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->builder);
	}

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$path = $this->builder->getIniPath();
		$this->assertEquals($this->filePath, $path);
	}

	/**
	 * @return null
	 */
	public function testBuildGoodRoutes()
	{
		$route = $this->builder->build('/');
		$this->assertInstanceOf(
			'Appfuel\App\Route\ActionRoute',
			$route
		);
		$this->assertEquals('/', $route->getRouteString());
		$this->assertEquals(
			'Appfuel\App\Action\Auth\System\Login', 
			$route->getActionNamespace()
		);
		$this->assertEquals('public', $route->getAccessPolicy());
		$this->assertEquals('html',  $route->getResponseType());

		$route = $this->builder->build('error/handler/invalid');
		$this->assertInstanceOf(
			'Appfuel\App\Route\ActionRoute',
			$route
		);
		$this->assertEquals('error/handler/invalid', $route->getRouteString());
		$this->assertEquals(
			'Appfuel\App\Action\Error\Handler\Invalid', 
			$route->getActionNamespace()
		);
		$this->assertEquals('public', $route->getAccessPolicy());
		$this->assertEquals('html',  $route->getResponseType());

		/* this is an example of an alias */
		$route = $this->builder->build('error');
		$this->assertInstanceOf(
			'Appfuel\App\Route\ActionRoute',
			$route
		);
		$this->assertEquals('error', $route->getRouteString());
		$this->assertEquals(
			'Appfuel\App\Action\Error\Handler\Invalid', 
			$route->getActionNamespace()
		);
		$this->assertEquals('public', $route->getAccessPolicy());
		$this->assertEquals('html',  $route->getResponseType());
	}

	/**
	 * When the route can not be found in the ini file the builder will
	 * return false.
	 *
	 * @return null
	 */
	public function testRouteNotFound()
	{
		$result = $this->builder->build('will/not/be/found');
		$this->assertFalse($result);
	}

	/**
	 * Available route in the ini file that deliberately left the namespace
	 * empty
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteFoundNamespaceEmpty()
	{
		$result = $this->builder->build('malformed/namespace');
		
	}

	/**
	 * Available route in the ini file that deliberately left the access
	 * empty
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteFoundAccessEmpty()
	{
		$result = $this->builder->build('malformed/access');
		
	}

	/**
	 * Available route in the ini file that deliberately left the return type
	 * empty
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteFoundResponseTypeEmpty()
	{
		$result = $this->builder->build('malformed/return-type');
		
	}

	/**
	 * Available route in the ini file but all fields are empty
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testRouteFoundAllFieldsEmpty()
	{
		$result = $this->builder->build('malformed/all-empty');
		
	}
}

