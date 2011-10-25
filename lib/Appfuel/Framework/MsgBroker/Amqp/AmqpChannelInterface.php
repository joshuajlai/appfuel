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
namespace Appfuel\Framework\MsgBroker\Amqp;

/**
 */
interface AmqpChannelInterface
{
	/**
	 * @return	array
	 */
	public function getProfile();

	/**
	 * @return	array
	 */
	public function getAdapter();

	/**
	 * Use the profile to declare the exchange, declare the queue and bind the
	 * queue to the exchange.
	 * @return null
	 */
	public function initialize();

}
