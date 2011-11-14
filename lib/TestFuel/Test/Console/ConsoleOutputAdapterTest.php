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
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleOutputAdapter;

/**
 * We will be testing to ensure data is written to STDOUT for render and 
 * STDERR for renderError and that isValidOutput works correctly
 */
class ConsoleOutputAdapterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	AppfuelError
	 */
	protected $adapter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->adapter = new ConsoleOutputAdapter();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->adapter = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Output\OutputAdapterInterface',
			$this->adapter
		);	
	}

	/**
	 * Provides both valid and invalid data
	 *
	 * @return	array
	 */
	public function provideContentData()
	{
		return array(
			array('',			true),
			array("\t",			true),
			array(PHP_EOL,		true),
			array("\n",			true),
			array(" \t\n",		true),
			array("my string",	true),
			array(12345,		true),
			array(1.233,		true),
			array(new SplFileInfo('path'), true),
			array(null,			false),
			array(array(),		false),
			array(array(1,2,3), false),
			array(new StdClass(), false),
		);
	}

	/**
	 * @dataProvider	provideContentData
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsValidOutput($output, $expected)
	{
		$result = $this->adapter->isValidOutput($output);
		$this->assertEquals($expected, $result);
	}
}
