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
	Appfuel\Framework\Env\ErrorReporting;

/**
 * The ErrorReporting class encapsulates the logic arround a single 
 * php method error_reporting. The intent is to create a uniform set of
 * of error levels that can be used in a config file.
 */
class ErrorReportingTest extends ParentTestCase
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
	 * List of php error constants. The 0 and -1 have no constants they
	 * represent 0 for no levels so don't report and -1 for all levels 
	 * @var array
	 */
	protected $levels = array(
        0,
        E_ERROR,
        E_WARNING,
        E_PARSE,
        E_NOTICE,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_STRICT,
		E_RECOVERABLE_ERROR,
        E_DEPRECATED,
        E_USER_DEPRECATED,
        E_ALL,
        -1
	);

	/**
	 * Save the current reporting level and display setting so they can be
	 * reverted during tear down.
	 *
	 * @return null
	 */
	public function setUp()
	{
		error_reporting(E_ALL | E_STRICT);
		$this->error = new ErrorReporting();
	}

	/**
	 * Revert the current reporting level and display settings
	 *
	 * @return null
	 */
	public function tearDown()
	{
		error_reporting($this->currentLevel);
		unset($this->error);
	}

	/**
	 * Test that the map is an associative array of level integers.
	 * 
	 * @return null
	 */
	public function testGetMap()
	{
		$map = $this->error->getMap();
		$this->assertInternalType('array', $map);
		$this->assertNotEmpty($map);

		foreach ($map as $code => $level) {
			$this->assertInternalType('string', $code);
			$this->assertInternalType('int', $level);
			$this->assertContains($level, $this->levels);
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
		$map = $this->error->getMap();
		foreach ($map as $code => $level) {
			$this->assertTrue($this->error->isCode($code));
		}

		/* prove value is not in codes */
		$code = 'no_in_code';
		$this->assertArrayNotHasKey($code, $map);
		$this->assertFalse($this->error->isCode($code));	
	}

	/**
	 * We know that every code in the map has a coresponding error level so
	 * we can test that code and expect that level in return
	 *
	 * @return null
	 */
	public function testMapCode()
	{
		$map = $this->error->getMap();
		foreach ($map as $code => $level) {
			$level = $this->error->mapCode($code);
			$this->assertEquals($map[$code], $level);	
		}


		$code = 'no_in_code';
		$this->assertFalse($this->error->mapCode($code));
	}

	/**
	 * We know that every code in the map has a coresponding error level so
	 * we can test that level and expect back that code
	 * @return null
	 */
	public function testMapLevel()
	{
		$map = $this->error->getMap();
		foreach ($map as $code => $level) {
			$code = $this->error->mapLevel($level);
			$this->assertEquals($map[$code], $level);	
		}


		$level = 999999999;
		$this->assertFalse($this->error->mapLevel($level));
	}

	public function testGetLevel()
	{
		$enabled  = null;
		$disabled = null;
		$result = $this->error->setLevel($enabled, $disabled);
		echo "\n", print_r($result,1), "\n";exit;
	}

	/**
	 * Test the ability to set/get the error_reporting. Loop through all codes
	 * and use to the level with setReportingLevel and compare the results
	 * of getReportingLevel with raw parameter set to true. They should be the
	 * same as what error_reporting shows
	 *
	 * @return null
	 */
	public function xtestGetSetReportingLevelRaw()
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
	public function xtestGetSetReportingLevel()
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
