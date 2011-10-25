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
namespace Hare;

use Appfuel\Framework\Exception,
	Appfuel\MsgBroker\Amqp\AmqpProfile;

/**
 */
class AdminProfile extends AmqpProfile
{
	/**
	 * @return	TMProfile
	 */
	public function __construct()
	{
		$exchange = array(
			'exchange'	=> 'hare',
			'type'		=> 'fanout',
			'durable'	=> true
		);
	
		$queue = array(
			'queue'		=> 'hare-tasks',
			'durable'	=> true,
		);
		parent::__construct($queue, $exchange);
	}
}
