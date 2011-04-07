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
namespace Test\Appfuel\Framework\Init;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Framework\Env\State;

/**
 * State is a value object used to hold the current state of the frameworks
 * environment.
 */
class PHPErrorTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var State
	 */
	protected $state = NULL;
	
	/**
	 * Value used for displaying errors 1st param in constructor
	 * @var string
	 */
	protected $displayErrors = 'on';

	/**
	 * Value used to error reporting 2nd param in constructor
	 * @var string
	 */
	protected $errorReporting = 'all_strict';

	/**
	 * Value used for default timezone 3rd param in constructor
	 * @var string
	 */
	protected $timezone = 'America\Los_Angeles';

	/**
	 * Value used for the autoload stack 4th param in constructor
	 * @var array
	 */
	protected $autoloadStack = array(1,2,3);

	/**
	 * Value used to hold the include path 5th and final param in constuctor
	 * @var string
	 */
	protected $includePath = 'some:path';

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->state = new State(
			$this->displayErrors,
			$this->errorReporting,
			$this->timezone,
			$this->autoloadStack,
			$this->includePath
		);
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
    public function testDisplayErrors()
    {

		$this->assertEquals(
			$this->displayErrors,
			$this->state->displayErrors(),
			'should be the same value given to the constructor'
		);
    }

    /**
	 * @return null
     */
    public function testErrorReporting()
    {

		$this->assertEquals(
			$this->errorReporting,
			$this->state->errorReporting(),
			'should be the same value given to the constructor'
		);
    }

    /**
	 * @return null
     */
    public function testTimezone()
    {

		$this->assertEquals(
			$this->timezone,
			$this->state->defaultTimezone(),
			'should be the same value given to the constructor'
		);
    }

    /**
	 * We are testing the autoload stack with a simple array of ints because
	 * this is a value object that only needs to pass back the values passed in
	 *
	 * @return null
     */
    public function testAutoloadStack()
    {

		$this->assertEquals(
			$this->autoloadStack,
			$this->state->autoloadStack(),
			'should be the same value given to the constructor'
		);
    }

    /**
	 * @return null
     */
    public function testIncludeaPath()
    {

		$this->assertEquals(
			$this->includePath,
			$this->state->includePath(),
			'should be the same value given to the constructor'
		);
    }
}
