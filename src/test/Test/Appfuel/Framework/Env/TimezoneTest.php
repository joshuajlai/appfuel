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

use Test\AfTestCase					  as ParentTestCase,
	Appfuel\Stdlib\Filesystem\Manager as FileManager,
	Appfuel\Registry,
	Appfuel\Framework\Env\Timezone;

/**
 * Test the ability to get and set the default timezone
 */
class TimezoneTest extends ParentTestCase
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
		$this->timezone = new Timezone();
	}

	/**
	 * Restore the include path and registry settings
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->timezone);
		$this->restoreAppfuelSettings();
	}

	/**
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

