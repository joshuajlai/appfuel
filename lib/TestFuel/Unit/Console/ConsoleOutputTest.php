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
	Appfuel\Console\ConsoleOutput,
	TestFuel\TestCase\BaseTestCase;

/**
 * We will be testing to ensure data is written to STDOUT for render and 
 * STDERR for renderError and that isValidOutput works correctly
 */
class ConsoleOutputTest extends BaseTestCase
{
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
			'Appfuel\Console\ConsoleOutputInterface',
			$this->output
		);
	}

	/**
	 * @dataProvider	provideAllStringsIncludingCastable
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsValidOutput($output)
	{
		$this->assertTrue($this->output->isValidOutput($output));
	}

	/**
	 * @dataProvider	provideNoCastableStrings
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsValidOutput_Failures($output)
	{
		$this->assertFalse($this->output->isValidOutput($output));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNoCastableStrings
	 * @depends				testInterface
	 * @return				null
	 */
	public function testRenderInvalidData($output)
	{
		$this->output->render($output);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNoCastableStrings
	 * @depends				testInterface
	 * @return				null
	 */
	public function testRenderErrorInvalidData($output)
	{
		$this->output->renderError($output);
	}
}
