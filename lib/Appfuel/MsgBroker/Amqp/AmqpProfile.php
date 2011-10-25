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
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface;

/**
 * The profile groups parameters needed for declaring an exchange and queue
 * and binding that queue to the exchange. This class can be used by itself
 * with config settings passed into the constructor or extend the class 
 * and hide the setting the the constructor.
 */
class AmqpProfile implements AmqpProfileInterface
{
    /**
     * Datastructure to hold the info required to declare an exchange
     * @var string
     */
    protected $exchange = array(
        'exchange'      => '',
        'type'          => 'direct',
        'passive'       => false,
        'durable'       => false,
        'auto-delete'   => true,
        'internal'      => false,
        'no-wait'       => false,
        'args'          => null,
        'ticket'        => null
    );

    /**
     * Datastructure to hold the info required to declare a queue
     * @var array
     */
    protected $queue = array(
        'queue'         => null,
        'passive'       => false,
        'durable'       => false,
        'exclusive'     => false,
        'auto-delete'   => true,
        'no-wait'       => false,
        'args'          => null,
        'ticket'        => null,
    );

    /**
     * Datastructure to hold the info required to bind a queue
     * @var arrray
     */
    protected $bind = array(
        'queue'     => null,
        'exchange'  => '',
        'route-key' => '',
        'no-wait'   => false,
        'args'      => null,
        'ticket'    => null
    );

	/**
     * @param   array   $exchange
     * @param   array   $queue
     * @param   array   $bind
	 * @return	AmqpProfile
	 */
	public function __construct(array $queue, 
								array $exchange = null,
								array $bind = null)
	{
        $this->setQueue($queue);
        if (! empty($exchange)) {
            $this->setExchange($exchange);
        }

        if (! empty($bind)) {
            $this->setQueueBind($bind);
        }
	}

	/**
	 * @return	string
	 */
	public function getQueueName()
	{
		return $this->queue['queue'];
	}

	/**
	 * @return	string
	 */
	public function getExchangeName()
	{
		return $this->exchange['exchange'];
	}

    /**
     * @return  array
     */
    public function getExchangeData()
    {
        return $this->exchange;
    }

    /**
     * @return array
     */
    public function getQueueData()
    {
        return $this->queue;
    }

    /**
     * @return array
     */
    public function getBindData()
    {
        return $this->bind;
    }

    /**
     * @param   array   $data
     * @return  null
     */
    protected function setQueue(array $data)
    {
        if (! isset($data['queue']) || empty($data['queue']) ||
            ! is_string($data['queue'])) {
            throw new Exception("queue must be non empty string");
        }

        $this->queue['queue'] = $data['queue'];
        $this->bind['queue']  = $data['queue'];

        if (isset($data['passive']) && true === $data['passive']) {
            $this->queue['passive'] = true;
        }

        if (isset($data['durable']) && true === $data['durable']) {
            $this->queue['durable'] = true;
        }

        if (isset($data['exclusive']) && true === $data['exclusive']) {
            $this->queue['exclusive'] = true;
        }

        if (isset($data['auto-delete']) && false === $data['auto-delete']) {
            $this->queue['auto-delete'] = false;
        }

        if (isset($data['no-wait']) && true === $data['no-wait']) {
            $this->queue['no-wait'] = true;
        }

        if (isset($data['args']) && is_array($data['args']) &&
            ! empty($data['args'])) {
            $this->queue['args'] = $data['args'];
        }

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->queue['ticket'] = $data['ticket'];
        }
    }

    /**
     * Validate the correct keys exist and change their value. When the key
     * does not exist the default values will be used. 
     *
     * @param   array   $exchange
     * @return  null
     */
    protected function setExchange(array $data)
    {
        if (isset($data['exchange']) && is_string($data['exchange'])) {
			$exchange = trim($data['exchange']);
			$this->bind['exchange']     = $exchange;
            $this->exchange['exchange'] = $exchange;
        }

        $validTypes = array('direct', 'fanout', 'topic', 'header');
        if (isset($data['type']) &&
            is_string($data['type']) &&
            in_array(strtolower($data['type']), $validTypes)) {
            $this->exchange['type'] = strtolower($data['type']);
        }

        if (isset($data['passive']) && true === $data['passive']) {
            $this->exchange['passive'] = true;
        }

        if (isset($data['durable']) && true === $data['durable']) {
            $this->exchange['durable'] = true;
        }

        if (isset($data['auto-delete']) && false === $data['auto-delete']) {
            $this->exchange['auto-delete'] = false;
        }

        if (isset($data['internal']) && true === $data['internal']) {
            $this->exchange['internal'] = true;
        }

        if (isset($data['no-wait']) && true === $data['no-wait']) {
            $this->exchange['no-wait'] = true;
        }

        if (isset($data['args']) && is_array($data['args']) &&
            ! empty($data['args'])) {
            $this->exchange['args'] = $data['args'];
        }

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->exchange['ticket'] = $data['ticket'];
        }
    }

    /**
     * @param   array   $data
     * @return  null
     */
    protected function setQueueBind(array $data)
    {
        if (isset($data['route-key']) && is_string($data['route-key'])) {
            $this->bind['route-key'] = trim($data['route-key']);
        }

        if (isset($data['no-wait']) && true === $data['no-wait']) {
            $this->bind['no-wait'] = true;
        }

        if (isset($data['args']) && is_array($data['args']) &&
            ! empty($data['args'])) {
            $this->bind['args'] = $data['args'];
        }

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->bind['ticket'] = $data['ticket'];
        }
    }
}
