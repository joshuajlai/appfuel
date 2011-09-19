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
namespace TestFuel\Test\Console;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleViewTemplate;

/**
 * Template used to hold the data going back to the console
 */
class ConsoleViewTemplateTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->template	= new ConsoleViewTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->template = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Console\ConsoleViewTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewCompositeTemplateInterface',
			$this->template
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ViewTemplateInterface',
			$this->template
		);
	}

	/**
	 * When no parameters are passed the formatter is a json formatter
	 * and no data is added. Also the code is 200 and text is OK
	 *
	 * @return	null
	 */
	public function testDefaultConstructor()
	{
		$formatter = $this->template->getViewFormatter();
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\TextFormatter',
			$formatter
		);

		$this->assertEquals(array(), $this->template->getAllAssigned());
		$this->assertTrue($this->template->templateExists('error'));
	}
}
