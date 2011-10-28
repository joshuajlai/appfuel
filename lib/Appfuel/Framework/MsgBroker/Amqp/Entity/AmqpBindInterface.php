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
 * Value object that models the amqp bind entity
 */
interface AmqpBindInterface
{
	/**
	 * @return	string
	 */
	public function getQueueName();

    /**
     * @return  string
     */
    public function getExchangeName();

    /**
     * @return  string
     */
    public function getRouteKey();

    /**
     * @return  bool
     */
    public function isNoWait();

    /**
     * @return  array | null when not set
     */
    public function getArguments();
}
