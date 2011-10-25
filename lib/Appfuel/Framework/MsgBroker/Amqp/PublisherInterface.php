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
 * Value object that holds valid connection data
 */
interface PublisherInterface extends AmqpTaskInterface
{
	/**
	 * @return	null
	 */	
	public function setMessage($msg);

	/**
	 * @return	null
	 */
	public function setRouteKey($key);
}
