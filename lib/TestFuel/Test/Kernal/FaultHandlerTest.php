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
namespace TestFuel\Test\Kernal;

use StdClass,
	Exception,
	Appfuel\Kernal\FaultHandler,
	TestFuel\TestCase\BaseTestCase;

/**
 * The kernal's fault handler deals with errors and exceptions, so we will
 * test its ability to handle both
 */
class FaultHandlerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var	FaultHandler
	 */
	protected $handler = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->handler = new FaultHandler();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->handler = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Kernal\FaultHandler',
			$this->handler
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructor()
	{
		$defaultLogger = $this->handler->getLogger();
		$this->assertInstanceOf(
			'Appfuel\Log\Logger',
			$defaultLogger
		);

		$this->assertInstanceOf(
			'Appfuel\Log\SysLogAdapter',
			$defaultLogger->getAdapter()
		);

		$logger  = $this->getMock("Appfuel\Log\LoggerInterface");
		$handler = new FaultHandler($logger);
		$this->assertSame($logger, $handler->getLogger());
	}
}
