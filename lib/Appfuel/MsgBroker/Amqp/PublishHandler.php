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
	Appfuel\Framework\MsgBroker\Amqp\PublishHandlerInterface;

/**
 * The core repsonsibility is found in the publish method. The publisher holds
 * all the information about exchanges, queues, bindings and publishing. The
 * handler sets up the channel (declare exchanges, declare queues and make 
 * one or more bindings of the queues and exchanges). Registers a shutdown 
 * method and publishes by registering the adapter method.
 */
class PublishHandler extends AbstractHandler
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
	public function publish($msg = null, $routeKey = null)
	{
		$publisher = $this->getTask();
        $adapter = $this->getChannelAdapter();
        if (! $adapter) {
            $err  = "Must initialize handler before channel can be setup ";
            $err .= "be registered";
            throw new Exception($err);
        }
		$this->setupChannel($adapter, $publisher);

		
		if (null !== $msg) {
			$publisher->setMessage($msg);
		}

		if (null !== $routeKey) {
			$publisher->setRouteKey($routeKey);
		}

		$this->registerShutDown();

		$params  = $publisher->getAdapterValues();
		$ok = call_user_func_array(array($adapter, $method), $values);
        if (false === $ok) {
            throw new Exception("failed call to basic_publish");
        }
		/* this is where the basic_publish is called */
		$this->registerAdapterMethod('basic_publish', $methodParams);
	}

	/**
	 * @string	msg
	 * @return	null
	 */
	public function setMessage($body)
	{
		$this->getTask()
			 ->setMessage($body);

		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	public function setRouteKey($key)
	{
		$this->getTask()
			 ->setRouteKey($key);

		return $this;
	}
}
