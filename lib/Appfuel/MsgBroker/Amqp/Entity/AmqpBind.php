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
	Appfuel\Framework\MsgBroker\Amqp\AmqpBindInterface;

/**
 * This is model of the amqp entity bind. It only provides information 
 * about the bind and does not perform any amqp operations. This is 
 * an immutable object whose state can only be set during instantiation. 
 */
class AmqpBind implements AmqpBindInterface
{
	/**
	 * Name of the queue to bind
	 * @var string
	 */
	protected $queueName = null;

	/**
	 * Name of the exchange to bind to
	 * @var string
	 */
	protected $exchangeName = null;

	/**
	 * Specifies the routing key for binding. The routing key is used for 
	 * routing messages depending on the exchange information. Not all 
	 * exchanges use a routing key. If the queue name is empty, the server
	 * uses the last queue declared on the channel. If the routing key is
	 * also empty the server uses the queue name for the routing key as well.
	 * The meaning of empty routing keys depends on the echange implementation
	 * @var string
	 */
	protected $routeKey = null;

	/**
	 * If set, the server will not repond to the method. The client should not 
	 * wait for a reply method. If the server could not complete the method it
	 * will raise a channel or connection exception
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
	 * @param	string	$queue
	 * @param	bool	$exchange
	 * @param	bool	$routeKey
	 * @param	bool	$noWait
	 * @param	array	$args	optional
	 * @return	Exchange
	 */
	public function __construct($queue,
								$exchange,
								$routeKey = '',
								$noWait = false,
								$args = null)
	{

		$err = 'Failed to create exchange object:';
		$this->setQueueName($queue, $err)
			 ->setExchangeName($exchange, $err)
			 ->setRouteKey($routeKey, $err)
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
	protected function setQueueName($name, $err)
	{
	    if (! is_string($name)) {
			throw new Exception("$err queue name must be a string");
		}
		$this->queueName = $name;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getExchangeName()
	{
		return $this->echangeName;
	}
	
	/**
	 * @throw	Appfuel\Framework\Exception
	 * @param	string	$name
	 * @param	string	$err	error message
	 * @return	AmqpExchange
	 */
	protected function setExchangeName($name, $err)
	{
	    if (! is_string($name)) {
			throw new Exception("$err exchange name must be a string");
		}
		$this->echangeName = $name;
		return $this;
	}


	/**
	 * @return	string
	 */
	public function getRouteKey()
	{
		return $this->routeKey;
	}

	/**
	 * @param	bool	$passive
	 * @param	string	$err
	 * @return	AmqpExchange
	 */
	protected function setRouteKey($routeKey = null, $err)
	{
		if (null === $routeKey) {
			$routeKey = '';
		}

		if (! is_string($routeKey)) {
			throw new Exception("$err route key must be a string");
		}
		$this->routeKey = $routeKey;
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
}
