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
namespace Test\Appfuel\App\Action;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Action\ActionBuilder,
	StdClass;

/**
 * The action builder encapsulates the logic needed to build a fully functional
 * action controller.
 */
class ActionBuilderTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var ActionBuilder
	 */
	protected $builder = null;

	/**
	 * @var Route
	 */
	protected $route = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->route   = $this->getMockRoute();
		$this->builder = new ActionBuilder($this->route);
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->route);
		unset($this->builder);
	}

	/**
	 * @return null
	 */
	public function testContructorGetRoute()
	{
		$this->assertSame($this->route, $this->builder->getRoute());
	}

	/**
	 * @return null
	 */
	public function testGetSetIsError()
	{
		$this->assertFalse($this->builder->isError());
		$this->assertNull($this->builder->getError());

		$error = 'this is an error';
		$this->assertSame(
			$this->builder,
			$this->builder->setError($error),
			'must use a fluent interface'
		);

		$this->assertTrue($this->builder->isError());
		$this->assertEquals($error, $this->builder->getError());

		$this->assertSame(
			$this->builder,
			$this->builder->clearError(),
			'must use a fluent interface'
		);

		$this->assertFalse($this->builder->isError());
		$this->assertNull($this->builder->getError());
	}
}

