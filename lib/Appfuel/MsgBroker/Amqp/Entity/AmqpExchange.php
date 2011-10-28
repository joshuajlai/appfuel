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
namespace Appfuel\MsgBroker\Amqp\Entity;

use Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpExchangeInterface;

/**
 * This is model of the amqp entity exchange. I only provides information 
 * about the exchange and does not perform any amqp operations. This is 
 * an immutable object whose state can only be set during instantiation. 
 */
class AmqpExchange implements AmqpExchangeInterface
{
	/**
	 * Name of the exchange the will be declared. The default exchange has
	 * a name that is an empty string
	 * @var string
	 */
	protected $name = '';

	/**
	 * Each exchange belongs to one of a set of exchange types implemented by
	 * the server. The exchange types define the functionality of the exchange
	 * @var string
	 */
	protected $type = 'direct';

	/**
	 * If set, the server will reply with Declare-Ok if the exchange already
	 * exists with the same name, and rais an error if not. The client can use
	 * this to check whether an exchange exists without modifying the server
	 * state. When set, all other method feilds except name and no-wait are
	 * ignored.
	 * @var bool
	 */
	protected $passive = false;

	/**
	 * If set when creating a new exchange, the exchange will be marked as 
	 * durable. Durable exchanges remain active when a server restarts. Non
	 * durable exchanges (transient) are purged if/when a server restarts.
	 * @var bool
	 */
	protected $durable = false;

	/**
	 * If set, the exchange is deleted when all queues have finished using it.
	 * The server will ignore this field if the exchange already exists
	 * @var bool
	 */
	protected $autoDelete = true;

	/**
	 * If set, the exchange may not be used directly by publishers, but only
	 * when bound to other exchanges. Internal exchanges are used to construct
	 * wiring that is not visible to applications
	 * @var bool
	 */
	protected $internal = false;

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
	 * This is part of the amqp php lib. Not sure what its for
	 * @var	int
	 */
	protected $ticket = null;

	/**
	 * Make all setting immutable
	 *
	 * @param	string	$name
	 * @param	string	$type	must be -(direct|fanout|topic|header)
	 * @param	bool	$passive
	 * @param	bool	$durable
	 * @param	bool	$autoDelete
	 * @param	bool	$internal
	 * @param	bool	$noWait
	 * @param	array	$args	optional
	 * @param	int		$ticket	optional
	 * @return	Exchange
	 */
	public function __construct($name = '',
								$type = 'direct',
								$passive = false,
								$durable = false,
								$autoDelete = true,
								$internal = false,
								$noWait = false,
								$args = null,
								$ticket = null)
	{

		$err = 'Failed to create exchange object:';
		$this->setExchangeName($name, $err)
			 ->setType($type, $err)
			 ->setPassive($passive, $err)
			 ->setDurable($durable, $err)
			 ->setAutoDelete($autoDelete, $err)
			 ->setInternal($internal, $err)
			 ->setNoWait($noWait, $err);

		if (null !== $args && ! empty($args) && is_array($args)) {
			$this->args = $args;
		}

		if (null !== $ticket && is_int($ticket)) {
			$this->ticket = $ticket;
		}
	}

	/**
	 * @return	string
	 */
	public function getExchangeName()
	{
		return $this->exchangeName;
	}
	
	/**
	 * @throw	Appfuel\Framework\Exception
	 * @param	string	$name
	 * @param	string	$err	error message
	 * @return	AmqpExchange
	 */
	protected function setExchangeName($name = null, $err)
	{
		/* default name when null is used */
		if (null === $name) {
			$name = '';
		}
	
	    if (! is_string($name)) {
			throw new Exception("$err name must be a string");
		}
		$this->exchangeName = $name;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @throw	Appfuel\Framework\Exception
	 * @param	string	$name
	 * @param	string	$err	error message
	 * @return	AmqpExchange
	 */
	protected function setType($type = null, $err)
	{
		$err = 'Failed to create exchange object:';
		/* default type when null is used */
		if (null === $type) {
			$type = 'direct';
		}

		if (! is_string($type)) {
			throw new Exception("$err type must be a string");
		}

		$type = strtolower($type);
		$valid = array('direct', 'fanout', 'topic', 'header');
		if (! in_array($type, $valid, true)) {
			$err .= " not a valid type. must be one of the following ".
			$err .= "-(". implode('|', $valid) . ")";
			throw new Exception($err);
		}
		$this->type = $type;
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
	public function isInternal()
	{
		return $this->internal;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setInternal($internal = null, $err)
	{
		if (null === $internal) {
			$internal = false;
		}

		if (! is_bool($internal)) {
			throw new Exception("$err internal must be a bool");
		}
		$this->internal = $internal;
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
