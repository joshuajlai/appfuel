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
	Appfuel\Framework\MsgBroker\Amqp\AmqpTaskInterface;

/**
 * Consumer is used by the ConsumeHandler where process is called and handled
 * during the handlers callback. The Consume Handler also uses the consumer
 * to intialize the channel. 
 */
abstract class AbstractTask implements AmqpTaskInterface
{	
	/**
	 * Method signature for adapterMethod
	 * @var array
	 */
	protected $adapterData = array();

	/**
	 * Name of the adapter method used for the task like basic_consume or
	 * basic_publish
	 *
	 * @var string
	 */
	protected $adapterMethod = null;

	/**
	 * @var AmqpProfileInterface
	 */
	protected $profile = null;
	
	/**
     * @param   AmqpProfileInterface	$profile
	 * @return	AbstractTask
	 */
	public function __construct(AmqpProfileInterface $profile, 
								$method,
								array $data = null)
	{

		$this->setAdapterMethod($method);
        $this->setProfile($profile);
		
		if (null === $data) {
			$data = array();
		}
		$this->setAdapterData($data);
	}

	/**
	 * @return	string
	 */
	public function getExchangeName()
	{
		return $this->profile->getExchangeName();
	}

	/**
	 * @return	string
	 */
	public function getQueueName()
	{
		return $this->profile->getQueueName();
	}

	/**
	 * @return	AmqpProfileInterface
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * @return	string
	 */
	public function getAdapterMethod()
	{
		return $this->adapterMethod;
	}

	/**
	 * @return	array
	 */
	public function getAdapterData()
	{
		return	$this->adapterData;
	}

	/**
	 * @return	array
	 */
	public function getAdapterValues()
	{
		return array_values($this->adapterData);
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
		return array_values($this->profile->getQueueData());
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
	 * @param	AmqpProfileInterface $profile
	 * @return	null
	 */
	protected function setProfile(AmqpProfileInterface $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$method
	 * @return	null
	 */
	protected function setAdapterMethod($method)
	{
		if (empty($method) || ! is_string($method)) {
			throw new Exception("adapter method must be a non empty string");
		}

		$this->adapterMethod = $method;
	}

	/**
	 * @param	array
	 * @return	null
	 */
	protected function setAdapterData(array $data)
	{
		$this->adapterData = $data;
	}
}
