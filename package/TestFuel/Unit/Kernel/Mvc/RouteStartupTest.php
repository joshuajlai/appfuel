<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RouteStartup;

class RouteStartupTest extends BaseTestCase
{
	/**
	 * @test
	 * @return RouteStartup
	 */
	public function createRouteStartup()
	{
		$startup = new RouteStartup();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteStartupInterface',
			$startup
		);

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	RouteStartup
	 */
	public function prependStartupTasks(RouteStartup $startup)
	{
		$this->assertFalse($startup->isPrependStartupTasks());
		
		$this->assertSame($startup, $startup->prependStartupTasks());
		$this->assertTrue($startup->isPrependStartupTasks());
		
		$this->assertSame($startup, $startup->appendStartupTasks());
		$this->assertFalse($startup->isPrependStartupTasks());

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	RouteStartup
	 */
	public function ignoreConfigStartupTasks(RouteStartup $startup)
	{
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());
		
		$this->assertSame($startup, $startup->ignoreConfigStartupTasks());
		$this->assertTrue($startup->isIgnoreConfigStartupTasks());
		
		$this->assertSame($startup, $startup->useConfigStartupTasks());
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	RouteStartup
	 */
	public function disableStartup(RouteStartup $startup)
	{
		$this->assertFalse($startup->isStartupDisabled());
		
		$this->assertSame($startup, $startup->disableStartup());
		$this->assertTrue($startup->isStartupDisabled());
		
		$this->assertSame($startup, $startup->enableStartup());
		$this->assertFalse($startup->isStartupDisabled());

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	RouteStartup
	 */
	public function startupTasks(RouteStartup $startup)
	{
		$this->assertFalse($startup->isStartupTasks());

		$tasks = array('MyTask', 'YourTask');
		$this->assertSame($startup, $startup->setStartupTasks($tasks));
		$this->assertEquals($tasks, $startup->getStartupTasks());
		$this->assertTrue($startup->isStartupTasks());

		$this->assertSame($startup, $startup->setStartupTasks(array()));
		$this->assertEquals(array(), $startup->getStartupTasks());

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	null
	 */
	public function setStartupTasksNotAStringFailure(RouteStartup $startup)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', 1234);
		$startup->setStartupTasks($tasks);
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	null
	 */
	public function setStartupTasksEmptyStringFailure(RouteStartup $startup)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', '');
		$startup->setStartupTasks($tasks);
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	RouteStartup
	 */
	public function excludedStartupTasks(RouteStartup $startup)
	{
		$this->assertFalse($startup->isExcludedStartupTasks());

		$tasks = array('MyTask', 'YourTask');
		$this->assertSame($startup, $startup->setExcludedStartupTasks($tasks));
		$this->assertEquals($tasks, $startup->getExcludedStartupTasks());
		$this->assertTrue($startup->isExcludedStartupTasks());

		$this->assertSame($startup, $startup->setExcludedStartupTasks(array()));
		$this->assertEquals(array(), $startup->getExcludedStartupTasks());

		return $startup;
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	null
	 */
	public function setExcludedStartupNotStrFailure(RouteStartup $startup)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', 1234);
		$startup->setExcludedStartupTasks($tasks);
	}

	/**
	 * @test
	 * @depends	createRouteStartup
	 * @return	null
	 */
	public function setExcludedStartupEmptyStrFailure(RouteStartup $startup)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', '');
		$startup->setExcludedStartupTasks($tasks);
	}




}
