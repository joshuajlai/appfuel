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
	AmqpChannel as AmqpChannelAdapter,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface;

/**
 * Adapter for the AMQPChannel
 */
class AmqpChannel implements AmqpChannelInterface
{
	/**
	 * Holds details about the exchange, queue and queue binding
	 * @var	AmqpProfileInterface
	 */
	protected $profile = null;

	/**
	 * @var	\AmqpChannel
	 */
	protected $adapter = null;

	/**
	 * @return	AmqpConnector
	 */
	public function __construct(AmqpChannelAdapter $adapter, 
								AmqpProfileInterface $profile)
	{
		$this->profile = $profile;
		$this->adapter = $adapter;
	}
	
	/**
	 * @return	AmqpProfileInterface
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * @return	AmqpChannelAdapter
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @return	
	 */
	public function close()
	{
		return $this->adapter->close();
	}

	/**
	 * @return null
	 */
	public function initialize()
	{
		$this->declareExchange();
		$this->declareQueue();
		$this->bindQueue();	
	}

	/**
	 * @reutrn	
	 */
	public function declareExchange()
	{
		return call_user_func_array(
			array($this->adapter, 'exchange_declare'),
			$this->getExchangeValues()
		);
	}

	/**
	 * @reutrn	
	 */
	public function declareQueue()
	{
		return call_user_func_array(
			array($this->adapter, 'queue_declare'),
			$this->getQueueValues()
		);
	}

	/**
	 * @reutrn	
	 */
	public function bindQueue()
	{
		return call_user_func_array(
			array($this->adapter, 'queue_bind'),
			$this->getBindValues()
		);
	}

	/**
	 * @return	array
	 */	
	public function getExchangeValues()
	{
		return array_values($this->profile->getExchangeData());
	}

	/**
	 * @return	array
	 */	
	public function getQueueValues()
	{
		return array_values($this->profile->getQueueData());
	}

	/**
	 * @return	array
	 */	
	public function getBindValues()
	{
		return array_values($this->profile->getBindData());
	}
}
