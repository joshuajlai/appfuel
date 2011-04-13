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
	Appfuel\Framework\Env\State;

/**
 * State is a value object used to hold the current state of the frameworks
 * environment. All the setters do not care what you put inside. Its up to 
 * other objects to get those values corrent, no type checking here.
 */
class StateTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var State
	 */
	protected $state = NULL;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->state = new State();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->state);
	}

    /**
	 * @return null
     */
    public function testErrorDisplay()
    {
		$this->assertFalse(
			$this->state->isErrorDisplay(),
			'Initial value of isErrorDisplay is always false'
		);

		$this->assertNull(
			$this->state->getErrorDisplay(),
			'Initial value of getErrorDisplay is always null'
		);

		$value = 'on';
		$this->assertSame(
			$this->state,
			$this->state->setErrorDisplay($value),
			'setter belongs to a fluent interface'
		);

		$this->assertTrue($this->state->isErrorDisplay());
		$this->assertEquals($value, $this->state->getErrorDisplay());
    }

    /**
	 * @return null
     */
    public function testErrorReporting()
    {
		$this->assertFalse(
			$this->state->isErrorReporting(),
			'Initial value of isErrorReporting is always false'
		);

		$this->assertNull(
			$this->state->getErrorReporting(),
			'Initial value of getErrorReporting is always null'
		);

		$value = 'all,strict';
		$this->assertSame(
			$this->state,
			$this->state->setErrorReporting($value),
			'setter belongs to a fluent interface'
		);

		$this->assertTrue($this->state->isErrorReporting());
		$this->assertEquals($value, $this->state->getErrorReporting());
    }

    /**
	 * @return null
     */
    public function testDefaultTimezone()
    {
		$this->assertFalse(
			$this->state->isDefaultTimezone(),
			'Initial value of isDefaultTimezone is always false'
		);

		$this->assertNull(
			$this->state->getDefaultTimezone(),
			'Initial value of getDefaultTimezone is always null'
		);

		$value = 'America/Los_Angeles';
		$this->assertSame(
			$this->state,
			$this->state->setDefaultTimezone($value),
			'setter belongs to a fluent interface'
		);

		$this->assertTrue($this->state->isDefaultTimezone());
		$this->assertEquals($value, $this->state->getDefaultTimezone());
    }

    /**
	 * @return null
     */
    public function testIncludePath()
    {
		$this->assertFalse(
			$this->state->isIncludePath(),
			'Initial value of isIncludePath is always false'
		);

		$this->assertNull(
			$this->state->getIncludePath(),
			'Initial value of getIncludePath is always null'
		);

		$value = 'somePath:someOthePath';
		$this->assertSame(
			$this->state,
			$this->state->setIncludePath($value),
			'setter belongs to a fluent interface'
		);

		$this->assertTrue($this->state->isIncludePath());
		$this->assertEquals($value, $this->state->getIncludePath());

		/* also check the includePathAction */
		$this->assertNull(
			$this->state->getIncludePathAction(),
			'Initial value of getIncludePathAction is always null'
		);

		$value = 'append';
		$this->assertSame(
			$this->state,
			$this->state->setIncludePathAction($value),
			'setter belongs to a fluent interface'
		);

		$this->assertEquals($value, $this->state->getIncludePathAction());
    }

    /**
	 * @return null
     */
    public function testAutoload()
    {
		$this->assertFalse(
			$this->state->isAutoloadEnabled(),
			'Initial value of isAutoloadEnabled is always false'
		);

		$this->assertFalse(
			$this->state->isAutoloadStack(),
			'Initial value of isAutoloadStack is always false'
		);


		$this->assertNull(
			$this->state->getAutoloadStack(),
			'Initial value of getAutoloadStack is always null'
		);

		$value = array('some_autoloader');
		$this->assertSame(
			$this->state,
			$this->state->setAutoloadStack($value),
			'setter belongs to a fluent interface'
		);

		$this->assertTrue($this->state->isAutoloadStack());
		$this->assertEquals($value, $this->state->getAutoloadStack());

		$this->assertSame(
			$this->state,
			$this->state->setEnableAutoload(true),
			'setter belongs to a fluent interface'
		);
		$this->assertTrue($this->state->isAutoloadEnabled());

		$this->state->setEnableAutoload(false);
		$this->assertFalse($this->state->isAutoloadEnabled());
    }
}
