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
namespace AdminConsumer;

use Appfuel\Framework\Exception,
	Appfuel\MsgBroker\Amqp\Consumer;

/**
 */
class AdminConsumer extends Consumer
{
	/**
	 * @param	AqpProfileInterface	$prof
     * @param   array				$data	
	 * @return	Consumer
	 */
	public function __construct(AmqpProfileInterface $profile = null)
	{
		if (null === $profile) {
			$profile = new AdminProfile();
		}

		parent::__construct($profile, $data);
	}

	public function process($msg)
	{
		echo "[x] starting proccessing: waiting for messages \n";
		echo "$msg \n";
		return true;
	}
}
