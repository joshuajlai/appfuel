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
namespace TesFuel\Test\Kernel\Error;

use Appfuel\Kernel\Error\ErrorDisplay,
	TestFuel\TestCase\FrameworkTestCase;

/**
 * ErrorDisplay provides a uniform interface for setting the configuration 
 * ini_get('display_errors') and ini_set('display_errors', 'some_value'). 
 * The problem this solves is normalizing many possible values for valid
 * on, off and error states into just one valid indicator for each category
 * 'on', 'off', 'stderr'.
 */
class DisplayErrorTest extends FrameworkTestCase
{
	/**
	 * System Under Test
	 * @var PHPError
	 */
	protected $error = NULL;

	/**
	 * Save the current reporting level and display setting so they can be
	 * reverted during tear down.
	 *
	 * @return null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->error = new ErrorDisplay();
	}

	/**
	 * Revert the current reporting level and display settings
	 *
	 * @return null
	 */
	public function tearDown()
	{
		parent::tearDown();
		unset($this->error);
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Error\ErrorDisplayInterface',
			$this->error
		);
	}

	/**
	 * Valid on values include 'on', 'yes', '1', 1. No matter which value
	 * you use the value used to in the ini_set call will be 'on'
	 *
	 * @return null
	 */
	public function testOnValues()
	{
		$values = $this->error->getValidOnValues();
		$this->assertInternalType('array', $values);
		$this->assertNotEmpty($values);
	
		foreach ($values as $value) {
			$this->assertTrue($this->error->isValidOn($value));
			
			$result = $this->error->getValue($value);
			$this->assertEquals('on', $result);

			/* case insensitive */
			$result = $this->error->getValue(strtoupper($value));
			$this->assertEquals('on', $result);

			$this->assertTrue($this->error->set($value));
			$this->assertEquals('on', ini_get('display_errors'));

			/* case insensitive */
			$this->assertTrue($this->error->set(strtoupper($value)));
			$this->assertEquals('on', ini_get('display_errors'));
		}

		$this->assertFalse($this->error->isValidOn('off'));
		$this->assertFalse($this->error->isValidOn('stderr'));
		
		$this->assertFalse($this->error->isValidOn('not_a_value'));
		$this->assertFalse($this->error->getValue('not_a_value'));
	}

	/**
	 * Valid off values include 'off', 'no', '0', 0. No matter which value
	 * you use the value used to in the ini_set call will be 'off'
	 *
	 * @return null
	 */
	public function testOffValues()
	{
		$values = $this->error->getValidOffValues();
		$this->assertInternalType('array', $values);
		$this->assertNotEmpty($values);
	
		foreach ($values as $value) {
			$this->assertTrue($this->error->isValidOff($value));
			
			$result = $this->error->getValue($value);
			$this->assertEquals('off', $result);

			/* case insensitive */
			$result = $this->error->getValue(strtoupper($value));
			$this->assertEquals('off', $result);

			$this->assertTrue($this->error->set($value));
			$this->assertEquals('off', ini_get('display_errors'));

			/* case insensitive */
			$this->assertTrue($this->error->set(strtoupper($value)));
			$this->assertEquals('off', ini_get('display_errors'));
		}

		$this->assertFalse($this->error->isValidOff('on'));
		$this->assertFalse($this->error->isValidOff('stderr'));
		$this->assertFalse($this->error->isValidOff('not_a_value'));
	}

	/**
	 * Valid error values include 'stderr', 'err', 'error', No matter which 
	 * value you use the value used to in the ini_set call will be 'stderr'
	 *
	 * @return null
	 */
	public function testErrorValues()
	{
		$values = $this->error->getValidErrorValues();
		$this->assertInternalType('array', $values);
		$this->assertNotEmpty($values);
	
		foreach ($values as $value) {
			$this->assertTrue($this->error->isValidError($value));
			
			$result = $this->error->getValue($value);
			$this->assertEquals('stderr', $result);

			/* case insensitive */
			$result = $this->error->getValue(strtoupper($value));
			$this->assertEquals('stderr', $result);

			$this->assertTrue($this->error->set($value));
			$this->assertEquals('stderr', ini_get('display_errors'));

			/* case insensitive */
			$this->assertTrue($this->error->set(strtoupper($value)));
			$this->assertEquals('stderr', ini_get('display_errors'));
		}

		$this->assertFalse($this->error->isValidError('on'));
		$this->assertFalse($this->error->isValidError('off'));
		$this->assertFalse($this->error->isValidError('not_a_value'));
	}

	/**
	 * When the value given to set can not be resolved into on, off or stderr
	 * then set will return false
	 *
	 * @return null
	 */
	public function testSetBadValue()
	{
		$current = ini_get('display_errors');
		$this->assertFalse($this->error->set('no_a_value'));
		$this->assertEquals($current, ini_get('display_errors'));
	}

	/**
	 * Test the convience method that enables error display
	 * 
	 * @return null
	 */
	public function testEnableErrorDisplay()
	{
		$result = $this->error->enable();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('on', $display);
		$this->assertEquals($display, $this->error->get());
	}

	/**
	 * Test the convience method that disables error display
	 * 
	 * @return null
	 */
	public function testDisableErrorDisplay()
	{
		$result = $this->error->disable();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('off', $display);
		$this->assertEquals($display, $this->error->get());
	}

	/**
	 * Test the convience method that sends error to stderr
	 * 
	 * @return null
	 */
	public function testSendToStdErr()
	{
		$result = $this->error->sendToStdError();
		$this->assertTrue($result);
		$display = ini_get('display_errors');
		$this->assertEquals('stderr', $display);
		$this->assertEquals($display, $this->error->get());
	}
}
