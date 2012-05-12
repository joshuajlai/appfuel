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
	Appfuel\Kernel\Mvc\RouteAction,
	Testfuel\TestCase\BaseTestCase;

class RouteActionTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidName()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
			array('')
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidMethod()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array('')		
		);
	}

	/**
	 * @return	RouteAction
	 */
	public function createRouteAction()
	{
		return new RouteAction();
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function routeActionInterface()
	{
		$action = $this->createRouteAction();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteActionInterface',
			$action
		);

		return $action;
	}

	/**
	 * @test
	 * @depends	routeActionInterface
	 * @return	RouteAction
	 */
	public function name(RouteAction $action)
	{
		$this->assertNull($action->getName());

		$name = 'MyAction';
		$this->assertSame($action, $action->setName($name));
		$this->assertEquals($name, $action->getName());

		$this->assertEquals($action, $action->clearName());
		$this->assertNull($action->getName());

		return $action;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidName
	 * @return			null
	 */
	public function setNameFailure($badName)
	{
		$msg = 'action name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$action = $this->createRouteAction();
		$action->setName($badName);
	}

	/**
	 * @test
	 * @depends		routeActionInterface
	 * @return		null
	 */
	public function map(RouteAction $action)
	{
		$this->assertTrue($action->isMapEmpty());
		$this->assertEquals(array(), $action->getMap());

		$map = array(
			'get' => 'MyGet',
			'put' => 'MyPut',
			'delete' => 'MyDelete'
		);
		$this->assertSame($action, $action->setMap($map));
		$this->assertEquals($map, $action->getMap());
		$this->assertFalse($action->isMapEmpty());
		$this->assertEquals($map['get'], $action->getNameInMap('get'));
		$this->assertEquals($map['put'], $action->getNameInMap('put'));
		$this->assertEquals($map['delete'], $action->getNameInMap('delete'));

		$this->assertFalse($action->getNameInMap('post'));

		$this->assertSame($action, $action->clearMap());
		$this->assertEquals(array(), $action->getMap());
		$this->assertTrue($action->isMapEmpty());

		return $action;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidMethod
	 * @return			null
	 */
	public function setMapInvalidMethodFailure($badMethod)
	{
		$msg = 'action map method must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$action = $this->createRouteAction();
		$map = array(
			'get'		=> 'MyGet',
			'put'		=> 'MyPut',
			'delete'	=> 'MyDelete',
			$badMethod	=> 'MyBadMethod'
		);
		$action->setMap($map);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidName
	 * @return			null
	 */
	public function setMapInvalidActionNameFailure($badName)
	{
		$msg = 'action map action must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$action = $this->createRouteAction();
		$map = array(
			'get'		=> 'MyGet',
			'put'		=> 'MyPut',
			'delete'	=> 'MyDelete',
			'bad'		=> $badName
		);
		$action->setMap($map);
	}

	/**
	 * @test
	 * @depends		routeActionInterface
	 * @return		null
	 */
	public function findActionEmptyMap(RouteAction $action)
	{
		$name = 'MyName';
		$action->setName($name);

		/* method is ignored */
		$this->assertEquals($name, $action->findAction('put'));
		$this->assertEquals($name, $action->findAction(12345));
		$this->assertEquals($name, $action->findAction());
	}

	/**
	 * @test
	 * @depends		routeActionInterface
	 * @return		null
	 */
	public function findActionNonEmptyMap(RouteAction $action)
	{
		$action->clearName();
		$map = array(
			'get'		=> 'MyGet',
			'put'		=> 'MyPut',
			'delete'	=> 'MyDelete',
		);
		$action->setMap($map);

		/* method is not ignored */
		$this->assertEquals($map['get'], $action->findAction('get'));
		$this->assertEquals($map['put'], $action->findAction('put'));
		$this->assertEquals($map['delete'], $action->findAction('delete'));
		$this->assertFalse($action->findAction());
		$this->assertFalse($action->findAction(12345));
		$this->assertFalse($action->findAction('post'));
		
	}



}
