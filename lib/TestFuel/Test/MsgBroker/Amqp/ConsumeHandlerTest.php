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
namespace TestFuel\Test\MsgBroker\Amqp;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\MsgBroker\Amqp\Dependency,
	Appfuel\MsgBroker\Amqp\AmqpProfile,
	Appfuel\MsgBroker\Amqp\ConsumerTask,
	Appfuel\MsgBroker\Amqp\ConsumeHandler;

/**
 * Common fucntionality for handling both publisher and consumers
 */
class ConsumeHandlerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpProfile
	 */
	protected $profile = null;

	/**
	 * System Under Test
	 * @var Appfuel\MsgBroker\Amqp\AbstractHandler
	 */
	protected $handler = null;

	/**
	 * Used to create mock abtract objects
	 * @var string
	 */
	protected $handlerClass = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$depend = new Dependency();
		$depend->load();

		$profile  = new AmqpProfile(array('queue' => 'af-test-queue'));
		$this->task  = new ConsumerTask($profile);
		$this->connData  = array(
			'host'		=> 'localhost',
			'user'		=> 'guest',
			'password'	=> 'guest',
			'vhost'		=> '/'
		);
		$params = array($this->connData, $this->task);
		$this->handler = new ConsumeHandler($this->connData, $this->task);
							
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->task    = null;
		$this->handler = null;
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Framework\MsgBroker\Amqp\TaskHandlerInterface',
            $this->handler
        );
    }
}
