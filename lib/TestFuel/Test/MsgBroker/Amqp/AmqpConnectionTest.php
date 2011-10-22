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
	Appfuel\MsgBroker\Amqp\AmqpConnection;

/**
 */
class AmqpConnectionTest extends BaseTestCase
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
		$this->connector = new AmqpConnector(array(
			'host'		=> 'localhost',
			'user'		=> 'unit_tester',
			'password'	=> 'w3bG33k3r',
			'vhost'		=> 'unit_test'
		));
		
		$this->connection = new AmqpConnection($this->connector);
		
	}

	public function tearDown()
	{
		$this->connection = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface',
			$this->connection
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetConnector()
	{
		$this->assertSame($this->connector, $this->connection->getConnector());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConnectCreateChannelAdapter()
	{
		$this->assertNull($this->connection->connect());
		$this->assertTrue($this->connection->isConnected());

		$adapter = $this->connection->createChannelAdapter();
		$this->assertInstanceOf(
			'\AmqpChannel',
			$adapter
		);

		$this->assertTrue($this->connection->close());
		$this->assertFalse($this->connection->isConnected());
		$this->assertNull($this->connection->createChannelAdapter());	
	}

}
