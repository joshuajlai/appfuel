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
	Appfuel\Framework\MsgBroker\Amqp\ConsumerInterface;

/**
 * Adapter for the AMQPChannel
 */
class ConsumeHandler 
	extends AbstractHandler
{
	/**
	 * @param	mixed	$conn	
	 * @param	ConsumerInterface	$consumer
	 * @return	ConsumeHandler
	 */
	public function __construct($conn, ConsumerInterface $consumer)
	{
		parent::__construct($conn, $consumer);
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
	public function consume()
	{
		/* ensure this handler is registered as the callback */
		$consumer = $this->getTask();
		$consumer->setCallback(array($this, 'callback'));
		
        $this->setupChannel();
        $this->registerShutDown();
        $this->registerAdapterMethod();

		$adapter = $this->getChannelAdapter();
		while (count($adapter->callbacks)) {
			$adapter->wait();
		}
	}

	/**
	 * @param	\AMQPMessage
	 * @return	null
	 */
	public function callback(AMQPMessage $msg)
	{
		$consumer = $this->getTask();
		$channel  = $msg->delivery_info['channel'];

		$deliveryTag = $msg->delivery_info['delivery_tag'];
		$consumerTag = $msg->delivery_info['consumer_tag'];
		$redeliver   = $msg->delivery_info['redelivered'];
		$routeKey    = $msg->delivery_info['routing_key'];

		$body = $msg->body;
		if ('af-consumer-quit' === $body) {
			$channel->basic_cancel($consumerTag);
			return;
		}
		$result = $consumer->process($body);
		$channel->basic_ack($deliveryTag);
	}
}
