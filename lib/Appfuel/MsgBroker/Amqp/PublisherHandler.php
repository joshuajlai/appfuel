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
	AmqpMessage,
	AmqpChannel as AmqpChannelAdapter,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface,
	Appfuel\Framework\MsgBroker\Amqp\ConsumeHandlerInterface;

/**
 * Adapter for the AMQPChannel
 */
class PublishHandler implements AmqpConsumeHandlerInterface
{
	/**
	 * Holds details about the exchange, queue and queue binding
	 * @var	PublisherInterface
	 */
	protected $publisher = null;

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
	 * The channel adapter is created by the connection which handles 
	 * delaring, binding, consuming and publishing
	 * @var	\AMQPChannel
	 */
	protected $channelAdapter = null;

	/**
	 * @return	AmqpConnector
	 */
	public function __construct($conn, PublisherInterface $consumer)
	{
		$this->consumer = $consumer;

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
	 * @return	ConsumerInterface
	 */
	public function getConsumer()
	{
		return $this->consumer;
	}

	/**
	 * @return	AmqpChannelAdapter
	 */
	public function getChannelAdapter()
	{
		return $this->channelAdapter;
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

	/**
	 * @return	bool
	 */
	public function isConnection()
	{
		if ($this->connection instanceof AmqpConnectionInterface) {
			return true;
		}

		return false;
	}

	/**
	 * @return	bool
	 */
	public function isChannelAdapter()
	{
		if ($this->channelAdapter instanceof AmqpChannelAdapter) {
			return true;
		}

		return false;
	}


	/**
	 * Connect and create the channel adapter
	 * @return	null
	 */
	public function initialize()
	{
		$conn = $this->getConnection();
		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$this->channelAdapter = $conn->createChannelAdapter();
	}

	/**
	 * Connect if we are not already connected. declare the exchange, queue
	 * and bind them which is down in the channel initialize. Override the
	 * the callback so the this object will handle the callback and use the
	 * the consumers interface to process the message. Also setup shutdown
	 * function to close down the amqp library that we are using
	 *
	 * @return null
	 */
	public function startToConsume()
	{
		if (! $this->isChannelAdapter()) {
			$err = "Can not perform action unit handler is initialized";
			throw new Exception($err);
		}

		$adapter = $this->getChannelAdapter();
		$this->declareExchange($adapter);
		$this->declareQueue($adapter);
		$this->bindQueue($adapter);

		/* ensure this handler is registered as the callback */
		$consumer->setCallback(array($this, 'callback'));

		call_user_func_array(
			array($adapter, $consumer->getAdapterMethod()), 
			$consumer->getAdapterValues()
		);


		register_shutdown_function(array($this, 'shutDown'));

		while (count($adapter->callbacks)) {
			$adapter->wait();
		}
	}

    /**
     * @return  
     */
    public function declareExchange(AmqpChannelAdapter $adapter)
    {
		$values = $this->getConsumer()
					   ->getExchangeValues();	
        
		return call_user_func_array(
            array($adapter, 'exchange_declare'),
            $values
        );
    }

    /**
     * @return  
     */
    public function declareQueue(AmqpChannelAdapter $adapter)
    {
		$values = $this->getConsumer()
					   ->getQueueValues();	
        
		return call_user_func_array(
            array($this->channelAdapter, 'queue_declare'),
            $values
        ); 
    }

    /**
     * @return  
     */
    public function bindQueue(AmqpChannelAdapter $adapter)
    {
		$values = $this->getConsumer()
					   ->getBindValues();	
        
		return call_user_func_array(
            array($this->channelAdapter, 'queue_bind'),
            $values
        ); 
    }

	/**
	 * @return	null
	 */
	public function shutDown()
	{
		$channel = $this->getChannelAdapter();
		if ($this->isChannelAdapter()) {
			$this->getChannelAdapter()
				 ->close();
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
	public function callback(AMQPMessage $msg)
	{
		$consumer = $this->getConsumer();
		$channel  = $msg->delivery_info['channel'];

		$deliveryTag = $msg->delivery_info['delivery_tag'];
		$consumerTag = $msg->delivery_info['consumer_tag'];
		$redeliver   = $msg->delivery_info['redelivered'];
		$routeKey    = $msg->delivery_info['routing_key'];
		$msgCount    = $msg->deliver_info['message_count'];

		$body = $msg->body;
		if ('af-consumer-quit' === $body) {
			$channel->basic_cancel($consumerTag);
			return;
		}

		$result = $consumer->process($body);
		$channel->basic_ack($deliveryTag);
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
