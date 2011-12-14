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
namespace TestFuel\Unit\Kernel\Error;

use Appfuel\Kernel\Error\ErrorLevel,
	TestFuel\TestCase\BaseTestCase;

/**
 * The ErrorLevel class encapsulates the logic arround a single 
 * php method error_reporting. The intent is to create a uniform set of
 * of error levels that can be used in a config file.
 */
class ErrorLevelTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var ErrorLevel
	 */
	protected $error = NULL;

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
		parent::setUp();
		$this->error = new ErrorLevel();
	}

	/**
	 * Revert the current reporting level and display settings
	 *
	 * @return null
	 */
	public function tearDown()
	{
		parent::tearDown();
		$this->error = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Error\ErrorLevelInterface',
			$this->error
		);
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

	/**
	 * This is a convience method used so you don't need to specify all
	 * 15 codes to turn off
	 *
	 * @return null
	 */
	public function testDisableAll()
	{
		/* set error reporting to something known */
		error_reporting(E_ALL | E_STRICT);
		
		$this->assertNull($this->error->disableAll());
		$this->assertEquals(0, error_reporting());
		
		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['enabled']);
		
		/* all 15 codes should appear in the disabled array */
		$this->assertEquals(15, count($result['disabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['disabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}

	}

	/**
	 * setLevel accepts codes as comma separated strings, but it will also
	 * detect two integers 0, and -1. We will focus on 0 for this test
	 * which will disable error reporting
	 *
	 * @return	null
	 */ 
	public function testSetLevelDisableAll()
	{
		/* set error reporting to a known value */
		error_reporting(E_ALL | E_STRICT);
		$this->assertNull($this->error->setLevel(0));
		$this->assertEquals(0, error_reporting());

		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['enabled']);
		
		/* all 15 codes should appear in the disabled array */
		$this->assertEquals(15, count($result['disabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['disabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}
	}

	/**
	 * This is a convience method used so you don't need to specify all
	 * 15 codes to turn on
	 *
	 * @return null
	 */
	public function testEnableAll()
	{
		/* disable error reporting */
		error_reporting(0);
		
		$this->assertNull($this->error->enableAll());
		$this->assertEquals(-1, error_reporting());
		
		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['disabled']);
		
		/* all 15 codes should appear in the disabled array */
		$this->assertEquals(15, count($result['enabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['enabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}

	}

	/**
	 * setLevel accepts codes as comma separated strings, but it will also
	 * detect two integers 0, and -1. We will focus on -1 for this test
	 * which will enable all error reporting
	 *
	 * @return	null
	 */ 
	public function testSetLevelEnableAll()
	{
		/* disable error reporting */
		error_reporting(0);
		$this->assertNull($this->error->setLevel(-1));
		$this->assertEquals(-1, error_reporting());

		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['disabled']);
		
		/* all 15 codes should appear in the enabled array */
		$this->assertEquals(15, count($result['enabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['enabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}

	}

	/**
	 * Included in the constant is the code all which will include
	 * all other codes except strict so if we do 'all,strict' its the
	 * same as using including all 15 codes
	 *
	 * @return null
	 */ 
	public function testSetLevelAllWithCodesShortHand()
	{
		/* disable error reporting */
		error_reporting(0);
		$this->assertNull($this->error->setLevel('all,strict'));
		$this->assertEquals(E_ALL|E_STRICT, error_reporting());

		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['disabled']);
		
		/* all 15 codes should appear in the enabled array */
		$this->assertEquals(15, count($result['enabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['enabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}
	}

	/**
	 * Use all 14 codes which is like uses enableAll call accept its done
	 * manually with bitwise ORs of each codes value
	 *
	 * @return null
	 */ 
	public function testSetLevelAllWithCodesAllCodes()
	{
		/* disable error reporting */
		error_reporting(0);

		/* 
		 * list of 15 codes. there are 15 but the last on all is
		 * special to indicate all codes but strict should be used
		 */
		$codes = 'error, warning, parse, notice, core_error,'     .   
				 'core_warning, compile_error, compile_warning, ' .
				 'user_error, user_warning, user_notice, strict,' .
				 'recoverable_error, deprecated, user_deprecated';

		$this->assertNull($this->error->setLevel($codes));
		$this->assertEquals(E_ALL|E_STRICT, error_reporting());

		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['disabled']);
		
		/* all 15 codes should appear in the enabled array */
		$this->assertEquals(15, count($result['enabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['enabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}

	}

	/**
	 * Test the ability to add all codes using all and strict. Also disable
	 * the error code and check to make sure real error level reflects.
	 * Test getLevel reports the one code in the disabled array and 14 in
	 * the enabled array
	 *
	 * @return null
	 */
	public function testSetLevelEnableDisableCode()
	{
		/* disable error reporting */
		error_reporting(0);

		$codes = 'all,strict,-error';
		$this->assertNull($this->error->setLevel($codes));

		$level = (E_ALL | E_STRICT) & ~E_ERROR; 
		$this->assertEquals($level, error_reporting());
		
		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);

		$this->assertInternalType('array', $result['enabled']);
		$this->assertInternalType('array', $result['disabled']);

		$this->assertEquals(1, count($result['disabled']));
		$this->assertContains('error', $result['disabled']);


		$expected = array(
			'warning',
			'parse',
			'notice',
			'core_error',
			'core_warning',
			'compile_error',
			'compile_warning',
			'user_error',
			'user_warning',
			'user_notice',
			'strict',
			'recoverable_error',
			'deprecated',
			'user_deprecated'
		);

		$this->assertEquals(count($expected), count($result['enabled']));
		foreach ($expected as $code) {
			$this->assertContains($code, $result['enabled']);
		}
	}

	/**
	 * Test what happens when the codes given to setLevel is an empty string
	 * 
	 * @return null
	 */	
	public function testSetGetLevelWithNoCodes()
	{
		error_reporting(E_ALL | E_STRICT);
		$this->assertNull($this->error->setLevel(''));

		/* prove nothing has changed */
		$this->assertEquals(E_ALL | E_STRICT, error_reporting());

		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertEmpty($result['disabled']);
		
		/* all 15 codes should appear in the enabled array */
		$this->assertEquals(15, count($result['enabled']));

		/* 
		 * each code in the disabled array should be a valid php error
		 * constant
		 */		
		foreach ($result['enabled'] as $code) {
			$this->assertContains($code, $this->levels);
		}
	}

	/**
	 * When you have codes that indicate they are to be disabled and
	 * there are no coded given from which to disable them from the
	 * setLevel will use the current reporting level instead
	 *
	 * @return null
	 */
	public function testSetGetLevelNoEnableWithDisable()
	{
		/* set current reporting level */
		error_reporting(E_ALL | E_STRICT);
		
		$codes = '-error, -warning, -parse';
		$this->assertNull($this->error->setLevel($codes));

		$level = (E_ALL | E_STRICT) & ~(E_ERROR | E_WARNING | E_PARSE);
		$this->assertEquals($level, error_reporting());


		$result = $this->error->getLevel();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('enabled', $result);
		$this->assertArrayHasKey('disabled', $result);
		$this->assertInternalType('array', $result['enabled']);
		$this->assertInternalType('array', $result['disabled']);

		$this->assertEquals(3, count($result['disabled']));
		$this->assertEquals(12, count($result['enabled']));

		$this->assertContains('error', $result['disabled']);	
		$this->assertContains('warning', $result['disabled']);	
		$this->assertContains('parse', $result['disabled']);	


		$expected = array(
			'notice',
			'core_error',
			'core_warning',
			'compile_error',
			'compile_warning',
			'user_error',
			'user_warning',
			'user_notice',
			'strict',
			'recoverable_error',
			'deprecated',
			'user_deprecated'
		);

		$this->assertEquals(count($expected), count($result['enabled']));
		foreach ($expected as $code) {
			$this->assertContains($code, $result['enabled']);
		}

	}
}
