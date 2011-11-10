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
	Appfuel\Log\SysLogAdapter,
	TestFuel\TestCase\BaseTestCase;

/**
 * Log entry represents a single log message to be written to the log
 */
class SysLogAdapterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var SysLogAdapter
	 */
	protected $adapter = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->adapter = new SysLogAdapter();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->adapter = null;
	}

	/**
	 * @return array
	 */
	public function providValidIdentity()
	{
		$value = 'my-identity';
		return array(
			array($value,			$value),
			array(" $value",		$value),
			array("$value ",		$value),
			array("\t$value",		$value),
			array("\t $value \t",	$value),
			array("\n$value\n",		$value),
		);	
	}

	/**
	 * @return array
	 */
	public function providInValidIdentity()
	{
		return array(
			array(''),
			array(' '),
			array("\t"),
			array("\t "),
			array("\n"),
			array(" \n"),
			array(" \t\n"),
			array(12345),
			array(1.234),
			array(array(1,2,3)),
			array(new StdClass())
		);	
	}

	/**
	 * @return array
	 */
	public function providValidOptions()
	{
		return array(
			array(LOG_CONS),
			array(LOG_NDELAY),
			array(LOG_ODELAY),
			array(LOG_PERROR),
			array(LOG_PID),
			array(LOG_CONS|LOG_NDELAY),
			array(LOG_CONS|LOG_ODELAY),
			array(LOG_CONS|LOG_PERROR),
			array(LOG_CONS|LOG_PID),
			array(LOG_CONS|LOG_NDELAY|LOG_PERROR),
			array(LOG_CONS|LOG_NDELAY|LOG_PID),
			array(LOG_CONS|LOG_NDELAY|LOG_PERROR|LOG_PID),
			array(LOG_CONS|LOG_ODELAY|LOG_PERROR|LOG_PID),
			array(LOG_CONS|LOG_ODELAY|LOG_PERROR),
			array(LOG_CONS|LOG_ODELAY|LOG_PID),
			array(LOG_NDELAY|LOG_PERROR),
			array(LOG_NDELAY|LOG_PID),
			array(LOG_ODELAY|LOG_PERROR),
			array(LOG_ODELAY|LOG_PID),
			array(LOG_PID|LOG_PERROR)
		);	
	}

	/**
	 * @return	array
	 */
	public function provideInvalidOptions()
	{
		return	array(
			array(''),
			array('no strings'),
			array(array(1,2,3)),
			array(array()),
			array(new StdClass()),
			array(false),
			array(true),
			array(LOG_CONS|LOG_NDELAY|LOG_ODELAY),
			array(LOG_PID|LOG_NDELAY|LOG_ODELAY),
			array(LOG_PERROR|LOG_NDELAY|LOG_ODELAY),
			array(LOG_PID|LOG_PERROR|LOG_NDELAY|LOG_ODELAY),
			array(LOG_CONS|LOG_PID|LOG_PERROR|LOG_NDELAY|LOG_ODELAY)
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidFacilities()
	{
		return array(
			array(LOG_AUTH),
			array(LOG_AUTHPRIV),
			array(LOG_CRON),
			array(LOG_DAEMON),
			array(LOG_KERN),
			array(LOG_LOCAL0),
			array(LOG_LOCAL1),
			array(LOG_LOCAL2),
			array(LOG_LOCAL3),
			array(LOG_LOCAL4),
			array(LOG_LOCAL5),
			array(LOG_LOCAL6),
			array(LOG_LOCAL7),
			array(LOG_LPR),
			array(LOG_MAIL),
			array(LOG_NEWS),
			array(LOG_SYSLOG),
			array(LOG_USER),
			array(LOG_UUCP)
		);
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
	 * @return	array
	 */
	public function provideInvalidFacilities()
	{
		return array(
            array(''),
            array('no strings'),
            array(array(1,2,3)),
            array(array()),
            array(new StdClass()),
            array(false),
            array(true),
			array(LOG_CONS),
			array(12345678909876545321),
			array(1.2345)
		);
	}


	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Log\LogAdapterInterface',
			$this->adapter
		);
	}

	/**
	 * Values set in the constructor when nothing is given
	 *
	 * @depends testInterface
	 * @return	null
	 */
	public function testDefaultValues()
	{
		$this->assertEquals('appfuel', $this->adapter->getIdentity());

		/* send to system console if problem writing to syslog, open 
		 * connection immediately and include the pid 
		 */
		$options = LOG_CONS | LOG_NDELAY | LOG_PID;
		$this->assertEquals($options, $this->adapter->getOptions());

		$facility = LOG_USER;
		$this->assertEquals($facility, $this->adapter->getFacility());
	}

	/**
	 * @dataProvider	providValidIdentity
	 * @depends			testInterface
	 * @param	int		$input
	 * @param	int		$expected
	 * @return			null
	 */
	public function testConstructorWithIdentity($input, $expected)
	{
		$adapter = new SysLogAdapter($input);
		$this->assertEquals($expected, $adapter->getIdentity());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		providInValidIdentity
	 * @depends				testInterface
	 * @param	int			$input
	 * @return				null
	 */
	public function testConstructorWithIdentity_Failure($input)
	{
		$adapter = new SysLogAdapter($input);
	}

	/**
	 * @dataProvider	providValidOptions
	 * @depends			testInterface
	 * @param	int		$input
	 * @return			null
	 */
	public function testConstructorWithOptions($input)
	{
		$adapter = new SysLogAdapter('appfuel', $input);
		$this->assertEquals($input, $adapter->getOptions());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidOptions
	 * @depends				testInterface
	 * @param	int			$input
	 * @return				null
	 */
	public function testConstructorWithOptions_Failures($input)
	{
		$adapter = new SysLogAdapter('appfuel', $input);
	}

	/**
	 * @dataProvider	provideValidFacilities
	 * @depends			testInterface
	 * @param	int		$input
	 * @return			null
	 */
	public function testConstructorWithFacility($input)
	{
		$adapter = new SysLogAdapter('appfuel', LOG_PID, $input);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidFacilities
	 * @depends				testInterface
	 * @param	int			$input
	 * @return				null
	 */
	public function testConstructorWithFacility_Failure($input)
	{
		$adapter = new SysLogAdapter('appfuel', LOG_PID, $input);
	}

	/**
	 * The system should always be able to open an close the log
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testOpenCloseLog()
	{
		$this->assertTrue($this->adapter->openLog());
		$this->assertTrue($this->adapter->closeLog());
	}

	/**
	 * @depends	testOpenCloseLog
	 * @return	null
	 */
	public function testWrite()
	{
		$this->adapter->openLog();
		$this->assertTrue($this->adapter->write("php unittest log", LOG_ERR));
		$this->adapter->closeLog();	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidMessages
	 * @return	null
	 */
	public function testWrite_EmptyStringFailure($msg)
	{
		$adapter = $this->adapter->write($msg, LOG_ERR);
	}
}
