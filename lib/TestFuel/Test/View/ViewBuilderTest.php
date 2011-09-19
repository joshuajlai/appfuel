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
namespace TestFuel\Test\View;

use StdClass,
	SplFileInfo,
	Appfuel\View\ViewBuilder,
	TestFuel\TestCase\BaseTestCase;

/**
 * Builds views for Console, Hml, Json 
 */
class ViewBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ViewBuilder
	 */
	protected $builder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->builder	= new ViewBuilder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->builder = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewBuilderInterface',
			$this->builder
		);
	}

	/**
	 * @return	null
	 */
	public function testBuildConsoleViewDefault()
	{
		$view = $this->builder->buildConsoleView();
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);
	}
}
