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
								AmqpProfileInterface, $profile)
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
	protected function getAdapter()
	{
		return $this->adapter;
	}
}
