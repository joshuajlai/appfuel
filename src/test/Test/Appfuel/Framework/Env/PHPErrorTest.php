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
namespace Test\Appfuel\Framework\Env;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Framework\Env\PHPError;

/**
 * 
 */
class PHPErrorTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var PHPError
	 */
	protected $error = NULL;

	/**
	 * Error level as reported by php. Used to restore the level
	 * @var int
	 */
	protected $currentLevel = NULL;

	/**
	 * Current display settings as used to be restore this setting
	 * @var string
	 */
	protected $currentDisplay = NULL;

	/**
	 * Save the current reporting level and display setting so they can be
	 * reverted during tear down.
	 *
	 * @return null
	 */
	public function setUp()
	{
		$this->currentDisplay = ini_get('display_errors');
		$this->currentLevel   = error_reporting();
		$this->error = new PHPError();
	}

	/**
	 * Revert the current reporting level and display settings
	 *
	 * @return null
	 */
	public function tearDown()
	{
		error_reporting($this->currentLevel);
		ini_set('display_errors', $this->currentDisplay);
		unset($this->error);
	}

	/**
	 * Test all permutations of the on state for display errors 
	 * return true
	 *
	 * @return null
	 */
	public function testIsValidDisplayValue()
	{
		$values = array(
			'on', 
			'ON', 
			'off',
			'yes',
			'no', 
			1,
			'1',
			0,
			'0',
			'stderr'
		);

		foreach ($values as $value) {
			$this->assertTrue($this->error->isValidDisplayValue($value));
		}

		$this->assertFalse($this->error->isValidDisplayValue('abc'));
	}

	/**
	 * Test the ability to set and get the display status. No matter
	 * which on or off value we use it will allways display as on or off
	 *
	 * @return null
	 */
	public function testSetGetDisplayStatusOff()
	{
		$values = array('off', 0, '0', 'no');
		foreach ($values as $value) {
			$result = $this->error->setDisplayStatus($value);
			$this->assertTrue($result);

			$display = ini_get('display_errors');
			$this->assertEquals($value, $display);
			$this->assertEquals($display, $this->error->getDisplayStatus());
		}

		/* prove bad values get ignored */
		$result = $this->error->setDisplayStatus('notMapped');
		$this->assertFalse($result);

		$display = ini_get('display_errors');
		$this->assertEquals($display, ini_get('display_errors'));
	}

	/**
	 * Test the ability to set and get the display status. No matter
	 * which on value we use it will allways display as 'on'
	 *
	 * @return null
	 */
	public function testSetGetDisplayStatusOn()
	{
		$values = array('on', 1, '1', 'yes');
		foreach ($values as $value) {
			$result = $this->error->setDisplayStatus($value);
			$this->assertTrue($result);

			$display = ini_get('display_errors');
			$this->assertEquals($value, $display);
			$this->assertEquals($display, $this->error->getDisplayStatus());
		}
		/* prove bad values get ignored */
		$result = $this->error->setDisplayStatus('notMapped');
		$this->assertFalse($result);

		$display = ini_get('display_errors');
		$this->assertEquals($display, ini_get('display_errors'));
	}

	/**
	 * @return null
	 */
	public function testSetGetDisplayStatusStdErr()
	{
		$result = $this->error->setDisplayStatus('stderr');
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('stderr', $display);
		$this->assertEquals($display, $this->error->getDisplayStatus());
	}

	/**
	 * Test the convience method that enables error display
	 * 
	 * @return null
	 */
	public function testEnableErrorDisplay()
	{
		$result = $this->error->enableErrorDisplay();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('on', $display);
		$this->assertEquals($display, $this->error->getDisplayStatus());
	}

	/**
	 * Test the convience method that disables error display
	 * 
	 * @return null
	 */
	public function testDisableErrorDisplay()
	{
		$result = $this->error->disableErrorDisplay();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('off', $display);
		$this->assertEquals($display, $this->error->getDisplayStatus());
	}

	/**
	 * Test the convience method that sends error to stderr
	 * 
	 * @return null
	 */
	public function testSendToStdErr()
	{
		$result = $this->error->sendToStdErr();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('stderr', $display);
		$this->assertEquals($display, $this->error->getDisplayStatus());
	}

	/**
	 * We do not need to check the specific codes as we do not have control
	 * over how they change. The code itself is required to be a string and 
	 * its value is required to be an integer. All codes are returned in an
	 * array
	 *
	 * @return null
	 */
	public function testGetCodes()
	{
		$codes = $this->error->getCodes();
		$this->assertInternalType('array', $codes);
	    $this->assertFalse(empty($codes));

		foreach ($codes as $code => $value) {
			$this->assertInternalType('string', $code); 
			$this->assertInternalType('int',    $value); 
		}
	}

	/**
	 * We know from the testing getCodes that all those codes are valid. It
	 * stands to reason that each of those codes will return true when used
	 * with isCode and any value not in that array will return false
	 *
	 * @return null
	 */
	public function testIsCode()
	{
		$codes = $this->error->getCodes();
		foreach ($codes as $code => $value) {
			$this->assertTrue($this->error->isCode($code));
		}

		/* prove value is not in codes */
		$code = 'no_in_code';
		$this->assertArrayNotHasKey($code, $codes);
		$this->assertFalse($this->error->isCode($code));	
	}

	/**
	 * We know that for each code in the codes returned by getCodes has the
	 * coresponding value. Foreach code in that list getLevel should return
	 * its level and any code not in that list should return false
	 *
	 * @return null
	 */
	public function testGetLevel()
	{
		$codes = $this->error->getCodes();
		foreach ($codes as $code => $value) {
			$level = $this->error->getLevel($code);
			$this->assertEquals($codes[$code], $level);	
		}


		$code = 'no_in_code';
		$this->assertFalse($this->error->getLevel($code));
	}

	/**
	 * Test the ability to set/get the error_reporting. Loop through all codes
	 * and use to the level with setReportingLevel and compare the results
	 * of getReportingLevel with raw parameter set to true. They should be the
	 * same as what error_reporting shows
	 *
	 * @return null
	 */
	public function testGetSetReportingLevelRaw()
	{
		/* get reporting level should return the same as the current level */
		$useRaw = true; 
		$currentLevel = error_reporting();
		$level = $this->error->getReportingLevel($useRaw);
		$this->assertEquals($currentLevel, $level);

		/* set the level manually and see if getReportingLevel is corrects */
		$codes  = $this->error->getCodes();
		foreach ($codes as $code => $level) {
			$previous = error_reporting();
			$result   = $this->error->setReportingLevel($level, $useRaw);
			$this->assertEquals($previous, $result);
			
			$result = $this->error->getReportingLevel($useRaw);
			$this->assertEquals(error_reporting(), $result);
		}

		$currentLevel = error_reporting();
		$result = $this->error->setReportingLevel(12345);
		$this->assertFalse($result);

		/* prove the current level has not been altered */
		$this->assertEquals($currentLevel, error_reporting());
	}

	/**
	 * Test the ability to set/get the error_reporting. Loop through all codes
	 * and use to code with setReportingLevel and compare the results
	 * of getReportingLevel with raw parameter set to false. They should be the
	 * same as what error_reporting shows
	 *
	 * @return null
	 */
	public function testGetSetReportingLevel()
	{
		/* set the level manually and see if getReportingLevel is corrects */
		$codes  = $this->error->getCodes();
		$useRaw = false; 
		foreach ($codes as $code => $level) {
			$previous = error_reporting();
			$result   = $this->error->setReportingLevel($code, $useRaw);
			$this->assertEquals($previous, $result);
			
			$result = $this->error->getReportingLevel($useRaw);
			$this->assertEquals($result, $code);
			$this->assertEquals(error_reporting(), $level);
		}

		$currentLevel = error_reporting();
		$currentCode  = $this->error->getCode($currentLevel);
		$result = $this->error->setReportingLevel('not_mapped');
		$this->assertFalse($result);

		/* 
		 * when a code is not mapped it is ignored so this should return
		 * the curent code for the current error level
		 */
		$this->assertEquals($currentCode, $this->error->getReportingLevel());

		/* prove the current level has not been altered */
		$this->assertEquals($currentLevel, error_reporting());
	}
}
