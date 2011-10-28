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
 * Value object that models the amqp queue entity
 */
interface AmqpQueueInterface
{
	/**
	 * @return	string
	 */
	public function getQueueName();

    /**
     * @return  bool
     */
    public function isPassive();

    /**
     * @return  bool
     */
    public function isDurable();

	/**
	 * @return	bool
	 */
	public function isExclusive();

    /**
     * @return  bool
     */
    public function isAutoDelete();

    /**
     * @return  bool
     */
    public function isNoWait();

    /**
     * @return  array | null when not set
     */
    public function getArguments();
}
