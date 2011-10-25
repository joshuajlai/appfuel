<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\MsgBroker\Amqp;

use	Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface;

/**
 * Adapter for the AMQPChannel
 */
class AmqpHandler implements AmqpHandlerInterface
{
	/**
	 * Holds details about the exchange, queue and queue binding
	 * @var	AmqpProfileInterface
	 */
	protected $profile = null;

	/**
	 * Value object used to descibe the connection details
	 * @var	AmqpConnectorInterface
	 */
	protected $connector = null;
	
	/**
	 * Conection controls the amqp connection and creates the channel 
	 * object.
	 * @var	AmqpConnectionInterface
	 */
	protected $connection = null;


	/**
	 * @return	AmqpConnector
	 */
	public function __construct($conn)
	{
		if ($conn instanceof AmqpConnectionInterface) {
			$this->connector  = $conn->getConnector();
			$this->connection = $conn;
		}
		else if ($conn instanceof AmqpConnectorInterface) {
			$this->connector  = $conn;
			$this->connection = $this->createConnection($conn); 
		}
		else if (is_array($conn)) {
			$this->connection = $this->createConnection($conn);
			$this->connector  = $this->connection->getConnector();
		}
		else {
			$err = "parameter must implement AmqpConnectorInterface | ";
			$err = "AmqpConnectionInterface"; 
			throw new Exception($err); 
		}
	}
	
	/**
	 * @return	AmqpProfileInterface
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * @param	AmqpProfileInterface $profile
	 * @return	AmqpHandler
	 */
	public function setProfile(AmqpProfileInterface $profile)
	{
		$this->profile = $profile;
		return $this;
	}

	/**
	 * @return	AmqpConnectorInterface
	 */
	public function getConnector()
	{
		return $this->connector;
	}

	/**
	 * @return	AmqpConnectionInterface
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	public function startToConsume(array $params = null)
	{
		$conn = $this->getConnection();
		if (! $conn->isConnected()) {
			$conn->connect();
		}
		$profile = $this->getProfile();
		$adapter = $conn->createChannelAdapter();
		$channel = new AmqpChannel($adapter, $profile);
		$channel->initialize();
		
		
		$queueName = $profile->getQueueName();
		if (empty($params)) {
			$params = array();
		}

		if (! isset($params['queue']) || ! is_string($params['queue'])) {
			$params['queue'] = $queueName;			
		}

		if (! isset($params['callback'])) {
			$params['callback'] = array($this, 'callback');			
		}


		$detail = new ConsumerDetail($params);
		call_user_func_array(
			array($detail, 'basic_consume'), 
			$detail->getValues()
		);


		register_shutdown_function(array($this, 'shutDown'));

		while (count($adapter->callbacks)) {
			$adapter->wait();
		}
	}


	/**
	 * @return	null
	 */
	public function shutDown()
	{
		$channel = $this->getChannel();
		if ($channel instanceof AmqpChannelInterface) {
			$channel->close();
		}

		$conn = $this->getConnection();
		if ($conn instanceof AmqpConnectionInterface) {
			$conn->close();
		}
	}

	/**
	 * @param	\AMQPMessage
	 * @return	null
	 */
	public function callback($msg)
	{
		$consumer = $this->getConsumer();
		$channel  = $msg->delivery_info['channel'];
		$deliveryTag = $msg->delivery_info['delivery_tag'];
		$consumerTag = $msg->delivery_info['consumer_tag'];

		$body = $msg->body;
		if ('af-consumer-quit' === $body) {
			$channel->basic_cancel($consumerTag);
		}

		$result = $consumer->process($msg->body);
				
		echo "\n", print_r($msg,1), "\n";exit;
	}

	/**
	 * @param	AmqpConnectorInterface $conn
	 * @return	AmqpConnection
	 */
	protected function createConnection(AmqpConnectorInterface $conn)
	{
		if (! ($conn instanceof AmqpConnectorInterface) || 
			! is_array($conn)) {
			$err = "must be a AmqpConnectorInterface or an associative array";
			throw new Exception($err);
		}
		
		if (is_array($conn)) {
			$conn = new AmqpConnector($conn);
		}
		return new AmqpConnection($conn);
	}
}
