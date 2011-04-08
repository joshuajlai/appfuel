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
 * environment.
 */
class StateTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var State
	 */
	protected $state = NULL;
	
	/**
	 * @var string
	 */
	protected $stateData = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->stateData = array(
            'include_path'			 => 'somepath',
            'include_path_action'	 => 'replace',
            'display_errors'		 => true,
            'error_reporting'		 => 'all_strict',
            'enable_autoloader'		 => 'true',
            'default_timezone'       => 'America\Los_Angeles'
		);
		$this->state = new State($this->stateData);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->state);
	}

    /**
	 * Test the conditions when error configuration exists. This is used by
	 * the state object in setup. We create an empty state object to prove
	 * what happens when no configuration exists
	 *
	 * @return null
     */
    public function testErrorConfiguration()
    {

		$this->assertTrue($this->state->isErrorConfiguration());

		$this->assertEquals(
			$this->stateData['display_errors'],
			$this->state->displayErrors(),
			'should be the same value given to the constructor'
		);

		$this->assertEquals(
			$this->stateData['error_reporting'],
			$this->state->errorReporting(),
			'should be the same value given to the constructor'
		);

	
		$state = new State(array());
		$this->assertFalse($state->isErrorConfiguration());
		$this->assertNull($state->displayErrors());
		$this->assertNull($state->errorReporting());
    }

    /**
	 * Test the conditions when the default timezone configuration exists
	 * and when it doesn't
	 *
	 * @return null
     */
    public function testTimezone()
    {
		$this->assertTrue($this->state->isTimezoneConfiguration());

		$this->assertEquals(
			$this->stateData['default_timezone'],
			$this->state->defaultTimezone(),
			'should be the same value given to the constructor'
		);
		
		$state = new State(array());
		$this->assertFalse($state->isTimezoneConfiguration());
		$this->assertNull($state->defaultTimezone());
    }

    /**
	 * Test the conditions when the autoload stack needs to be restored. Which
	 * means the autoload stack is present in the state. This is not the case
	 * in setup so we will create a new state to test that condition
	 *
	 * @return null
     */
    public function testAutoloadStack()
    {
		$this->assertFalse($this->state->isRestoreAutoloaders());
		$this->assertNull(
			$this->state->autoloadStack(),
			'we did not include the autoload stack so this should be null'
		);

		$data = array(
			'autoload_stack' => array(
				'load' => 'some_load_method'
			)
		);
		$state = new State($data);
		$this->assertTrue($state->isRestoreAutoloaders());
		$this->assertEquals(
			$data['autoload_stack'],
			$state->autoloadStack(),
			'should be the autoload_stack array passed into the constructor'
		);
    }

    /**
	 * More commonly used than the autoloadStack is the isEnableAutoloader flag
	 * which is used by the framework during initialization to determine is the
	 * framework should enable the autoloader. This flag is given in setup.
	 *
	 * @return null
     */
    public function testIsEnableAutoloader()
    {
		$this->assertTrue($this->state->isEnableAutoloader());
		
		$state = new State(array());
		$this->assertFalse($state->isEnableAutoloader());
    }

    /**
	 * @return null
     */
    public function testIncludeaPath()
    {
		$this->assertTrue($this->state->isIncludePathConfiguration());
		$this->assertEquals(
			$this->stateData['include_path'],
			$this->state->includePath(),
			'should be the same value given to the constructor'
		);

		$this->assertEquals(
			$this->stateData['include_path_action'],
			$this->state->includePathAction(),
			'should be the same value given to the constructor'
		);

		$state = new State(array());
		$this->assertFalse($state->isIncludePathConfiguration());
		$this->assertNull($state->includePath());
		$this->assertNull($state->includePathAction());
    }
}
