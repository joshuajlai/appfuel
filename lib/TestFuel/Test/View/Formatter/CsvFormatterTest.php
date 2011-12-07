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
	Appfuel\View\Formatter\CsvFormatter;

/**
 * The text formmater converts and array of key=>value pairs into a string
 */
class CsvFormatterTest extends BaseTestCase
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
		$this->formatter = new CsvFormatter();
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
			'Appfuel\View\Formatter\ViewFormatterInterface',
			$this->formatter
		);
	}

	/**
	 * @return	null
	 */
	public function testArrayData()
	{
		$data = array(
			array('foo', 'bar', 'baz'),
			array('biz', 'wiz', 'kiz')
		);

		$expected = "foo,bar,baz" . PHP_EOL . "biz,wiz,kiz" . PHP_EOL;
		$this->assertEquals($expected, $this->formatter->format($data));
	}
}
