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

use AmqpMessage,
	Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\ConsumerTaskInterface;

/**
 * The core repsonsibility is found in the consume method. The consumer holds
 * all the information about exchanges, queues, bindings and consuming. The
 * handler sets up the channel (declare exchanges, declare queues and make 
 * one or more bindings of the queues and exchanges). Registers a shutdown 
 * method and consumes by registering the adapter method and entering a 
 * a callback loop for processing.
 */
class ConsumeHandler extends AbstractHandler
{
	/**
	 * @param	mixed	$conn	
	 * @param	ConsumerInterface	$consumer
	 * @return	ConsumeHandler
	 */
	public function __construct($conn, ConsumerTaskInterface $consumer)
	{
		parent::__construct($conn, $consumer);
	}
	
	/**
	 * @return null
	 */
	public function consume()
	{
		/* ensure this handler is registered as the callback */
		$consumer = $this->getTask();
		$adapter = $this->getChannelAdapter();
        if (! $adapter) {
            $err  = "Must initialize handler before channel can be setup ";
            $err .= "be registered";
            throw new Exception($err);
        }

		$consumer->setCallback(array($this, 'callback'))
				 ->enableManualAck();

        $this->setupChannel($adapter, $consumer);
        $this->registerShutDown();
		

		$params = $consumer->getAdapterValues();
		$ok = call_user_func_array(array($adapter, 'basic_consume'), $params);
		if (false === $ok) {
			throw new Exception("failed call to basic_consume");
		}

		while (count($adapter->callbacks)) {
			$adapter->wait();
		}
	}

	/**
	 * The callback loop uses the consumer's process method and passes the
	 * body of the message into it and interprets the response
	 *
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
		$worker = $consumer->getProcess($body);
		$channel->basic_ack($deliveryTag);
	}
}
