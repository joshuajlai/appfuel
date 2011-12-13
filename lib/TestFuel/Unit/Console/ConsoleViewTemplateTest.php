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
namespace TestFuel\Unit\Console;

use StdClass,
	SplFileInfo,
	Appfuel\Console\ConsoleViewTemplate,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class ConsoleViewTemplateTest extends BaseTestCase
{	
	/**
	 * System Under Test
	 * @var ConsoleViewTemplate
	 */
	protected $view = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->view = new ConsoleViewTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->view = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{	
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplateInterface',
			$this->view
		);

		$this->assertInstanceOf('Appfuel\View\ViewTemplate',$this->view);

		$formatter = $this->view->getViewFormatter();
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\TextFormatter',
			$formatter
		);
	}
}
