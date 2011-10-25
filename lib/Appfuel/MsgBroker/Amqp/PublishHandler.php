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
	Appfuel\Framework\MsgBroker\Amqp\PublisherInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface,
	Appfuel\Framework\MsgBroker\Amqp\PublishHandlerInterface;

/**
 */
class PublishHandler 
	extends AbstractHandler
{
	/**
	 * @param	mixed	$conn	
	 * @param	ConsumerInterface	$consumer
	 * @return	ConsumeHandler
	 */
	public function __construct($conn, PublisherInterface $publisher)
	{
		parent::__construct($conn, $publisher);
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
	public function publish($msg, $routeKey = null)
	{
		$this->setMessage($msg);
		if (null !== $routeKey) {
			$this->setRouteKey($routeKey);
		}

		$this->setupChannel();
		$this->registerShutDown();
		$this->registerAdapterMethod();
	}

	/**
	 * @string	msg
	 * @return	null
	 */
	public function setMessage($body)
	{
		$publisher = $this->getTask();
		$publisher->setMessage(new AMQPMessage($body));	
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	public function setRouteKey($key)
	{
		$publisher = $this->getTask();
		$publisher->setRouteKey($key);
	}
}
