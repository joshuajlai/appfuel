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
	Appfuel\Log\Logger,
	TestFuel\TestCase\BaseTestCase;

/**
 * The logger uses a log adapter to open, close and write to the log
 */
class LoggerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Logger
	 */
	protected $logger = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->logger = new Logger();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->logger = null;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidMessages()
	{
		return array(
			array(''),
			array(' '),
			array("\n"),
			array("\t"),
			array(" \t\n"),
			array(12345),
			array(1.2345),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Log\LoggerInterface',
			$this->logger
		);
	}

	/**
	 * When nothing is given the system will create a SysLogAdapter for you
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetAdapter()
	{
		$this->assertInstanceOf(
			'Appfuel\Log\SysLogAdapter',
			$this->logger->getAdapter()
		);

		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$logger  = new Logger($adapter);

		$this->assertSame($adapter, $logger->getAdapter());

		$this->assertNull($this->logger->setAdapter($adapter));
		$this->assertSame($adapter, $this->logger->getAdapter());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogEntryAdapterWontOpen()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(false));

		$this->logger->setAdapter($adapter);

		$entry = $this->getMock("Appfuel\Log\LogEntryInterface");
		
		$this->assertFalse($this->logger->logEntry($entry));
	}
	
	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogEntryFailedWrite()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(true));

		$adapter->expects($this->once())
				->method('writeEntry')
				->will($this->returnValue(false));

		$this->logger->setAdapter($adapter);

		$entry = $this->getMock("Appfuel\Log\LogEntryInterface");
		$this->assertFalse($this->logger->logEntry($entry));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogEntryWrite()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(true));

		$adapter->expects($this->once())
				->method('writeEntry')
				->will($this->returnValue(true));

		$this->logger->setAdapter($adapter);

		$entry = $this->getMock("Appfuel\Log\LogEntryInterface");
		$this->assertTrue($this->logger->logEntry($entry));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogFailed()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(true));

		$adapter->expects($this->once())
				->method('writeEntry')
				->will($this->returnValue(false));

		$this->logger->setAdapter($adapter);

		$this->assertFalse($this->logger->log('this is text', LOG_INFO));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogPass()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(true));

		$adapter->expects($this->once())
				->method('writeEntry')
				->will($this->returnValue(true));

		$this->logger->setAdapter($adapter);

		$this->assertTrue($this->logger->log('this is text', LOG_INFO));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogEntry()
	{
		$adapter = $this->getMock("Appfuel\Log\LogAdapterInterface");
		$adapter->expects($this->once())
				->method('openLog')
				->will($this->returnValue(true));

		$adapter->expects($this->once())
				->method('writeEntry')
				->will($this->returnValue(true));

		$this->logger->setAdapter($adapter);

		$entry = $this->getMock("Appfuel\Log\LogEntryInterface");
		$this->assertTrue($this->logger->log($entry));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLogNotEntryNotString()
	{
		$this->assertTrue($this->logger->log(new StdClass()));
	}



}
