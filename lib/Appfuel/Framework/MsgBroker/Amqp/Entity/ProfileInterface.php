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
namespace Appfuel\Framework\MsgBroker\Amqp\Entity;

/**
 * Profile holds the exchange, queue and binding entities and is used by
 * the channel setup operation to declare the exchange, declare the queue
 * and making any bindings
 */
interface ProfileInterface
{
	/**
	 * @return	AmqpQueueInterface
	 */
	public function getQueue();

	/**
	 * @return	AmqpExchangeInterface
	 */
	public function getExchange();

	/**
	 * @return	array
	 */
	public function getBindings();
}
