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
namespace TestFuel\Test\Framework\Env;

use TestFuel\TestCase\FrameworkTestCase,
	Appfuel\Framework\Env\Timezone;

/**
 * Test the ability to get and set the default timezone
 */
class TimezoneTest extends FrameworkTestCase
{
	/**
	 * System under test
	 * @var	Timezone
	 */
	protected $timezone = NULL;

	/**
	 * Save the include path and registry settings
	 * @return null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->timezone = new Timezone();
	}

	/**
	 * Restore the include path and registry settings
	 * @return null
	 */
	public function tearDown()
	{
		parent::tearDown();
		unset($this->timezone);
	}

	/**
	 * App the name of each valid timezone php knows about to ensure they
	 * are accepted
	 *
	 * @return null
	 */
	public function testSetDefault()
	{
		$tzList = timezone_identifiers_list();
		foreach ($tzList as $timezone) {
			$this->assertTrue($this->timezone->setDefault($timezone));
			$this->assertEquals($timezone, $this->timezone->getDefault());
		} 
	}

	/**
	 * @expectedException \Exception
	 */
	public function testBadTimezone()
	{
        $this->assertFalse($this->timezone->setDefault('blahs'));
	}
}
