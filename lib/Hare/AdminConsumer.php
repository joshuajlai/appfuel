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
	Appfuel\MsgBroker\Amqp\Consumer;

/**
 */
class AdminConsumer extends Consumer
{
	/**
	 * @return	Consumer
	 */
	public function __construct()
	{
		parent::__construct(new AdminProfile);
	}

	public function process($msg)
	{
		echo "[x] starting proccessing: waiting for messages \n";
		echo "$msg \n";
		return true;
	}
}
