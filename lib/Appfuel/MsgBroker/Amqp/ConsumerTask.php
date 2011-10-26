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
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\ConsumerTaskInterface;

/**
 * Consumer is used by the ConsumeHandler where process is called and handled
 * during the handlers callback. The Consume Handler also uses the consumer
 * to intialize the channel. 
 */
class ConsumerTask extends AbstractTask 
{
    /**
     * Datastructure to hold the info required for method call to basic_consume
     * @var string
     */
    protected $adapterData = array(
        'queue'			=> '',
        'consumer-tag'  => '',
        'no-local'      => false,
        'no-ack'		=> false,
        'exclusive'		=> true,
        'no-wait'		=> false,
        'callback'      => null,
        'ticket'        => null
    );

	/**
	 * Set the adapter method to basic_consume. This method is found in the
	 * \AMQPChannel object.
	 *
	 * @param	AqpProfileInterface	$prof
     * @param   array				$data	
	 * @return	Consumer
	 */
	public function __construct(AmqpProfileInterface $prof, array $data = null)
	{
		parent::__construct($prof, $data);
	}

	/**
	 * @param	scalar	$tag
	 * @return	ConsumerTask
	 */
	public function setConsumerTag($tag)
	{
		if (! is_scalar($tag)) {
			throw new Exception("consumer tag must be a scalar value");
		}
	
		$this->adapterData['consumer-tag'] = $tag;
		return $this;
	}

	/**
	 * @return	scalar
	 */
	public function getConsumerTag()
	{
		return $this->adapterData['consumer-tag'];
	}

	/**
	 * This puts the responsibility of the consumer task to send back an 
	 * acknowledgement onces processing is complete
	 *
	 * @return	ConsumerTask
	 */
	public function enableManualAck()
	{
		$this->adapterData['no-ack'] = true;
		return $this;
	}

	/**
	 * Rabbitmq will take the message out of memory as soon as it as been
	 * delivered to the consumer
	 *
	 * @return	ConsumerTask
	 */
	public function disableManualAck()
	{
		$this->adapterData['no-ack'] = false;
		return $this;
	}

	/**
	 * @param	mixed	$callback
	 * @return	null
	 */
	public function setCallback($callback)
	{
		if (! is_callable($callback)) {
			throw new Exception("callback must be callable");
		}

		$this->adapterData['callback'] = $callback;
	}

	/**
	 * Data holds the parameters used in basic_comsume for the channel adapter
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	array	$data 
	 * @return	null
	 */
	protected function setAdapterData(array $data)
	{
		$profile   = $this->getProfile();
		$queueName = $profile->getQueueName();
		if (! isset($data['queue']) || ! is_string($data['queue'])) {
			$data['queue'] = $queueName;
		}

		$this->adapterData['queue'] = trim($data['queue']);
		
		if (isset($data['consumer-tag']) && is_string($data['consumer-tag'])) {
			$this->adapterData['consumer-tag'] = $data['consumer-tag'];	
		}

		if (isset($data['no-local']) && true === $data['no-local']) {
			$this->adapterData['no-local'] = true;	
		}

		if (isset($data['no-ack']) && true === $data['no-ack']) {
			$this->adapterData['no-ack'] = true;	
		}

		if (isset($data['exclusive']) && true === $data['exclusive']) {
			$this->adapterData['exclusive'] = true;	
		}

		if (isset($data['no-wait']) && true === $data['no-wait']) {
			$this->adapterData['no-wait'] = true;	
		}

		if (isset($data['callback']) && is_callable($data['callback'])) {
			$this->adapterData['callback'] = $data['callback'];
		}

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->adapterData['ticket'] = $data['ticket'];
        }
	}
}
