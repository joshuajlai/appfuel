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
namespace TaskManager;

use Appfuel\Framework\Exception,
	Appfuel\MsgBroker\Amqp\Publisher;

/**
 */
class AdminPublisher extends Publisher
{
	/**
	 * @return	AdminPublisher
	 */
	public function __construct()
	{
		parent::__construct(new AdminProfile);
	}

}
