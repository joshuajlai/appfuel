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
	Appfuel\Console\ConsoleOutput,
	TestFuel\TestCase\BaseTestCase;

/**
 * We will be testing to ensure data is written to STDOUT for render and 
 * STDERR for renderError and that isValidOutput works correctly
 */
class ConsoleOutputTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	ConsoleOutput
	 */
	protected $output = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->output = new ConsoleOutput();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->output = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Kernel\OutputInterface',
			$this->output
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidData()
	{
		return array(
			array(''),
			array("\t"),
			array(PHP_EOL),
			array("\n"),
			array(" \t\n"),
			array("my string"),
			array(12345),
			array(1.233),
			array(true),
			array(false),
			array(new SplFileInfo('path')),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidData()
	{
		return array(
			array(null),
			array(array(1,2,3)),
			array(array()),
			array(new StdClass()),
		);
	}

	/**
	 * @dataProvider	provideValidData
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsValidOutput($output)
	{
		$this->assertTrue($this->output->isValidOutput($output));
	}

	/**
	 * @dataProvider	provideInvalidData
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsValidOutput_Failures($output)
	{
		$this->assertFalse($this->output->isValidOutput($output));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidData
	 * @depends				testInterface
	 * @return				null
	 */
	public function testRenderInvalidData($output)
	{
		$this->output->render($output);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidData
	 * @depends				testInterface
	 * @return				null
	 */
	public function testRenderErrorInvalidData($output)
	{
		$this->output->renderError($output);
	}
}
