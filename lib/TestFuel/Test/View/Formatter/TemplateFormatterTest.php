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
namespace TestFuel\Test\View\Formatter;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Formatter\TemplateFormatter;

/**
 * The template formatter converts a php template into a string. It binds 
 * itself with the template file by doing an include call which makes the 
 * parser class the $this reference in the template file. All functions except
 * format and include support the functionality of the template file
 */
class TemplateFormatterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var TextFormatter
	 */
	protected $formatter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$filePath = $this->getTestFilesPath() . '/ui/appfuel/template.phtml';
		$this->formatter = new TemplateFormatter($filePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->formatter = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\View\Formatter\ViewFormatterInterface',
			$this->formatter
		);
	}
}
