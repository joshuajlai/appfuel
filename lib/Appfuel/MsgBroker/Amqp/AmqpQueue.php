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

use Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpQueueInterface;

/**
 * This is model of the amqp entity queue. It only provides information 
 * about the queue and does not perform any amqp operations. This is 
 * an immutable object whose state can only be set during instantiation. 
 */
class AmqpQueue implements AmqpQueueInterface
{
	/**
	 * Name of the queue the will be declared. This must be set a null or
	 * empty name is invalid.
	 * @var string
	 */
	protected $name = null;

	/**
	 * If set, the server will reply with Declare-Ok if the queue already 
	 * exists with the same name, and raise an error if not. The client can use
	 * this to check whether an queue exists without modifying the server
	 * state. When set, all other method feilds except name and no-wait are
	 * ignored. A declare with both passive and no-wait has not effect.
	 * @var bool
	 */
	protected $passive = false;

	/**
	 * If set when creating a new queue, the queue will be marked as durable. 
	 * Durable queues remain active when a server restarts. Non durable queues 
	 * (transient) are purged if/when a server restarts. Note that durable 
	 * queues do not necessarily hold persistent messages, although it does not
	 * make sense to send a persistent message to a tranient queue.
	 * @var bool
	 */
	protected $durable = false;

	/**
	 * Exclusive queues may only be accessed by the current connection, and
	 * are deleted when that connection closes Passive declaration of an 
	 * exclusive queue by other connections are not allowed
	 * @var bool
	 */
	protected $exclusive = false;

	/**
	 * If set, the exchange is deleted when all queues have finished using it.
	 * The server will ignore this field if the exchange already exists
	 * @var bool
	 */
	protected $autoDelete = true;

	/**
	 * If set, the server will not repond to the method. The client should not 
	 * wait for a reply method. If the server could not complete the method it
	 * will rais a channel or connection exception
	 * @var bool
	 */
	protected $noWait = false;

	/**
	 * A set of arguments for the declaration. The syntax and semantics of 
	 * these depends on the server
	 * @var array
	 */
	protected $args = null;

	/**
	 * Make all setting immutable
	 *
	 * @param	string	$name
	 * @param	bool	$passive
	 * @param	bool	$durable
	 * @param	bool	$exclusive
	 * @param	bool	$autoDelete
	 * @param	bool	$noWait
	 * @param	array	$args	optional
	 * @return	Exchange
	 */
	public function __construct($name = '',
								$passive = false,
								$durable = false,
								$exclusive = false,
								$autoDelete = true,
								$noWait = false,
								$args = null)
	{

		$err = 'Failed to create exchange object:';
		$this->setQueueName($name, $err)
			 ->setPassive($passive, $err)
			 ->setDurable($durable, $err)
			 ->setExclusive($exclusive, $err)
			 ->setAutoDelete($autoDelete, $err)
			 ->setNoWait($noWait, $err);

		if (null !== $args && ! empty($args) && is_array($args)) {
			$this->args = $args;
		}
	}

	/**
	 * @return	string
	 */
	public function getQueueName()
	{
		return $this->queueName;
	}
	
	/**
	 * @throw	Appfuel\Framework\Exception
	 * @param	string	$name
	 * @param	string	$err	error message
	 * @return	AmqpExchange
	 */
	protected function setQueueName($name = null, $err)
	{
		/* default name when null is used */
		if (null === $name) {
			$name = '';
		}
	
	    if (! is_string($name)) {
			throw new Exception("$err name must be a string");
		}
		$this->queueName = $name;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPassive()
	{
		return $this->passive;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setPassive($passive = null, $err)
	{
		if (null === $passive) {
			$passive = false;
		}

		if (! is_bool($passive)) {
			throw new Exception("$err passive must be a bool");
		}
		$this->passive = $passive;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isDurable()
	{
		return $this->durable;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setDurable($durable = null, $err)
	{
		if (null === $durable) {
			$durable = false;
		}

		if (! is_bool($durable)) {
			throw new Exception("$err durable must be a bool");
		}
		$this->durable = $durable;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isExclusive()
	{
		return $this->exclusive;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setExclusive($exclusive = null, $err)
	{
		if (null === $exclusive) {
			$exclusive = false;
		}

		if (! is_bool($exclusive)) {
			throw new Exception("$err internal must be a bool");
		}
		$this->exclusive = $exclusive;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isAutoDelete()
	{
		return $this->autoDelete;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setAutoDelete($autoDelete = null, $err)
	{
		if (null === $autoDelete) {
			$autoDelete = true;
		}

		if (! is_bool($autoDelete)) {
			throw new Exception("$err autoDelete must be a bool");
		}
		$this->autoDelete = $autoDelete;
		return $this;
	}


	/**
	 * @return	bool
	 */
	public function isNoWait()
	{
		return $this->noWait;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setNoWait($noWait = null, $err)
	{
		if (null === $noWait) {
			$noWait = false;
		}

		if (! is_bool($noWait)) {
			throw new Exception("$err internal must be a bool");
		}
		$this->noWait = $noWait;
		return $this;
	}

	/**
	 * @return	array | null when not set
	 */
	public function getArguments()
	{
		return $this->args;
	}

	/**
	 * @return	int | null when not set
	 */
	public function getTicket()
	{
		return $this->ticket;
	}
}
