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
namespace TestFuel\Unit\Log;

use StdClass,
	SplFileInfo,
	Appfuel\Log\LogPriority,
	TestFuel\TestCase\BaseTestCase;

/**
 * Appfuel defines a log priority to be any sys log priority. If this does
 * not work for you, implement your own custom priority with the 
 * Appfuel\Log\LogPriorityInterface. This will be a simple test showing 
 * we can create a priority object for any syslog priority and an exception
 * will be thrown for anything that is not.
 */
class LogPriorityTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideLevels()
	{
		return array(
			array(LOG_EMERG),
			array(LOG_ALERT),
			array(LOG_CRIT),
			array(LOG_ERR),
			array(LOG_WARNING),
			array(LOG_NOTICE),
			array(LOG_INFO),
			array(LOG_DEBUG),
		);
	}

	/**
	 * @return	array
	 */
	public function provideBadLevels()
	{
		return array(
			array(null),
			array(''),
			array("\t"),
			array("i am a string"),
			array(1234565789998),
			array(1.233),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @dataProvider	provideLevels
	 * @param	string	$input
	 * @return	null
	 */
	public function testValidPriorities($level)
	{
		$priority = new LogPriority($level);
		$this->assertEquals($level, $priority->getLevel());
		$this->assertEquals((string)$level, $priority->__toString());
	}

	/**
	 * Anything that can not be understood as a syslog priority is set to
	 * LOG_INFO
	 *
	 * @dataProvider	provideBadLevels
	 * @param	string	$input
	 * @return	null
	 */
	public function testInvalidPriorities($level)
	{
		$priority = new LogPriority($level);
		$this->assertEquals(LOG_INFO, $priority->getLevel());
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$priority = new LogPriority(LOG_INFO);
		$this->assertInstanceOf(
			'Appfuel\Log\LogPriorityInterface',
			$priority
		);
	}

	/**
	 * The defualt priority is LOG_INFO
	 *
	 * @return	null
	 */
	public function testDefaultValue()
	{
		$priority = new LogPriority();
		$this->assertEquals(LOG_INFO, $priority->getLevel());
	}

}
