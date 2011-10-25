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
namespace Appfuel\MsgBroker\Amqp\Consume;

use Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\Consume\ConsumerInterface;

/**
 * Consumer is used by the ConsumeHandler where process is called and handled
 * during the handlers callback. The Consume Handler also uses the consumer
 * to intialize the channel. 
 */
class Consumer implements ConsumerInterface
{
    /**
     * Datastructure to hold the info required for method call to basic_consume
     * @var string
     */
    protected $data = array(
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
	 * @var AmqpProfileInterface
	 */
	protected $profile = null;
	
	/**
     * @param   array   $data
	 * @return	ConsumerDetail
	 */
	public function __construct(AmqpProfileInterface $profile, 
								array $data = null)
	{
        $this->setProfile($profile);
		$this->setData($data);
	}

	/**
	 * @return	AmqpProfileInterface
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * Values needed for the declare_exchange method of the amqplib
	 *
	 * @return	array
	 */
	public function getExchangeValues()
	{
		return array_values($this->profile->getExchangeData());
	}

	/**
	 * Values needed for the declare_queue method of the amqplib
	 *
	 * @return	array
	 */
	public function getQueueValues()
	{
		return array_values($this->profile->getExchangeData());
	}

	/**
	 * Values needed for the declare_queue method of the amqplib
	 *
	 * @return	array
	 */
	public function getBindValues()
	{
		return array_values($this->profile->getBindData());
	}

    /**
     * @return  array
     */
    public function getConsumeData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getConsumeValues()
    {
        return array_values($this->data);
    }

	public function setCallback($callback)
	{
		if (! is_callable($callback)) {
			throw new Exception("callback must be callable");
		}

		$this->data['callback'] = $callback;
	}

	/**
	 * @param	AmqpProfileInterface $profile
	 * @return	null
	 */
	protected function setProfile(AmqpProfileInterface $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * Data holds the parameters used in basic_comsume for the channel adapter
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	array	$data 
	 * @return	null
	 */
	protected function setData(array $data)
	{
		$profile = $this->getProfile();
		if (! ($profile instanceof AmqpProfileInterface)) {
			throw new Exception("Profile should be set before data");
		}
		$queueName = $profile->getQueueName();
		
		if (! isset($data['queue']) || ! is_string($data['queue'])) {
			$data['queue'] = $queueName;
		}

		$this->data['queue'] = trim($data['queue']);
		
		if (isset($data['consumer-tag']) && is_string($data['consumer-tag'])) {
			$this->data['consumer-tag'] = $data['consumer-tag'];	
		}

		if (isset($data['no-local']) && true === $data['no-local']) {
			$this->data['no-local'] = true;	
		}

		if (isset($data['no-ack']) && true === $data['no-ack']) {
			$this->data['no-ack'] = true;	
		}

		if (isset($data['exclusive']) && true === $data['exclusive']) {
			$this->data['exclusive'] = true;	
		}

		if (isset($data['no-wait']) && true === $data['no-wait']) {
			$this->data['no-wait'] = true;	
		}

		if (isset($data['callback']) && is_callable($data['callback'])) {
			$this->data['callback'] = $data['callback'];
		}

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->data['ticket'] = $data['ticket'];
        }
	}
}
