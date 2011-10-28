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
	Appfuel\Framework\MsgBroker\Amqp\Entity\ProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpBindInterface,
	Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpQueueInterface,
	Appfuel\Framework\MsgBroker\Amqp\Entity\AmqpExchangeInterface;

/**
 * The entity profile holds the exchange, queue and zero or more binding
 * interfaces. These are used by a ChannelInitializer which knows how to 
 * declare the exchange, queues and do the bindings
 */
class Profile implements ProfileInterface
{
    /**
     * @var AmqpExchangeInterface
     */
    protected $exchange = null;

    /**
     * @var AmqpQueueInterface
     */
    protected $queue = null;

    /**
	 * List of AmqpBindInterfaces
     * @var array
     */
    protected $bindings = array();

	/**
     * @param   array   $exchange
     * @param   array   $queue
     * @param   array   $bind
	 * @return	AmqpProfile
	 */
	public function __construct(AmqpQueueInterface $queue,
								AmqpExchangeInterface $exchange = null,
								array $bindings = null)
	{
		$this->setQueue($queue);

        if (empty($exchange)) {
			$exchange = new AmqpExchange();
		}
        $this->setExchange($exchange);

        if (! empty($bindings)) {
			foreach ($bindings as $bind) {
				$this->addBinding($bind);
			}
        }
	}

	/**
	 * @return	string
	 */
	public function getQueueName()
	{
		return $this->queue->getQueueName();
	}

	/**
	 * @return	string
	 */
	public function getExchangeName()
	{
		return $this->exchange->getExchangeName();
	}

    /**
     * @return  array
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return array
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @param   array   $data
     * @return  null
     */
    protected function setQueue(AmqpQueueInterface $queue)
    {
		$this->queue = $queue;
		return $this;
    }

    /**
     * @return  null
     */
    protected function setExchange(AmqpExchangeInterface $exchange)
    {
		$this->exchange = $exchange;
		return $this;
    }
}
