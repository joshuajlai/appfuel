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
namespace TestFuel\Test\Log;

use StdClass,
	SplFileInfo,
	Appfuel\Log\LogEntry,
	TestFuel\TestCase\BaseTestCase;

/**
 * Log entry represents a single log message to be written to the log
 */
class LogEntryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var LogEntry
	 */
	protected $entry = null;

	/**
	 * The main text of the entry
	 * @var string
	 */
	protected $msg = null;

	/**
	 * Used to test the timestamp generated when the object is created
	 * @var int
	 */
	protected $timestamp = null;
	
	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->msg = "this is a log";
		$this->timestamp = strtotime('now');
		$this->entry = new LogEntry($this->msg);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->entry = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Log\LogEntryInterface',
			$this->entry
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertEquals($this->msg, $this->entry->getText());
		$priority = $this->entry->getPriority();
		$this->assertInstanceof('Appfuel\Log\LogPriority', $priority);
		$this->assertEquals(LOG_INFO, $priority->getLevel());
		$this->assertEquals(LOG_INFO, $this->entry->getPriorityLevel());

		$timestamp = $this->entry->getTimestamp();
		$date  = date('d-m-Y H:i:s', $this->timestamp);
		$this->assertEquals($date, date('d-m-Y H:i:s', $timestamp));

		$expected = "[$date] {$this->entry->getText()}";
		$this->assertEquals($expected, $this->entry->getEntry());
	}

	/**
	 * @return	array
	 */
	public function provideTextValues()
	{
		return array(
			array('',			''),
			array(" ",			''),
			array(" \t",		''),
			array(" \n",		''),
			array("i am a log", "i am a log"),
			array("\t i am a log \n", "i am a log"),
			array(12345,		"12345"),
			array(1.234,		"1.234"),
			array(new SplFileInfo('path'), "path"),
		);
	}

	/**
	 * @dataProvider	provideTextValues
	 * @param	mixed	$input
	 * @param	mixed	$expected
	 * @return	null
	 */
	public function testTextValues($input, $expected)
	{
		$entry = new LogEntry($input);
		$this->assertEquals($expected, $entry->getText());
	}

	/**
	 * @expectedException	RunTimeException
	 * @return	null
	 */
	public function testInvalidTextArray_Failure()
	{
		$entry = new LogEntry(array(1,2,3));
	}

	/**
	 * @expectedException	RunTimeException
	 * @return	null
	 */
	public function testInvalidTextArray_ObjectNotCallable()
	{
		$entry = new LogEntry(new StdClass());
	}
}
