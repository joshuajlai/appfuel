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
	AmqpChannel as AmqpChannelAdapter,
	AmqpConnection as AmqpConnectAdapter,
	Appfuel\MsgBroker\Amqp\AmqpConnector,
	Appfuel\MsgBroker\Amqp\AmqpProfile,
	Appfuel\MsgBroker\Amqp\AmqpChannel,
	Appfuel\MsgBroker\Amqp\AmqpConnection;

/**
 * The channel is a wrapper for \AmqpChannel The main difference bettwen these
 * classes is that the wrapper uses the \AmqpChannel as an adapter and also
 * needs the AmqpProfile which holds all the info to declare an exchange, 
 * declare a queue and bind that queue
 */
class AmqpChannelTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AmqpConnection
	 */
	protected $connection = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$dependency = new Dependency();
		$dependency->load();
		$connector = new AmqpConnector(array(
			'host'		=> 'localhost',
			'user'		=> 'unit_tester',
			'password'	=> 'w3bG33k3r',
			'vhost'		=> 'unit_test'
		));

		$this->connection = new AmqpConnection($connector);
		$this->connection->connect();
		$this->assertTrue($this->connection->isConnected());

		$this->adapter = $this->connection->createChannelAdapter();
		$this->profile = new AmqpProfile(
			array('queue'	 => 'my-test-queue', 'auto-delete' => false),
			array('exchange' => 'my-tester-exchange', 'type' => 'direct', 'auto-delete' => false)
		);
	
		$this->channel = new AmqpChannel($this->adapter, $this->profile);	
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->connection->close();
		$this->connection = null;
		$this->adapter = null;
		$this->channel = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface',
			$this->channel
		);
	}
}
