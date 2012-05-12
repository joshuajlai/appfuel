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

/**
 * Used to connect has a guest to vhost / and a given host.
 */
class GuestConnector extends AmqpConnector
{
	/**
	 * @return	AmqpConnector
	 */
	public function __construct($host = 'localhost')
	{
		$data = array(
			'host'      => $host,
			'port'      => 5672,
			'user'      => 'guest',
			'password'  => 'guest',
			'vhost'     => '/'
		);

		parent::__construct($data);
	}
}
