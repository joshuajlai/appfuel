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
 * A task can either be a publisher or consumer. This class encapsulates the
 * logic needed by both. All tasks must be given a profile which can only
 * be set from the constructor and retrieved through a getter. The adapter
 * data is an associative array of key/value pairs where the values map
 * to the function paramters of basic_consume for consumers and basic_publish
 * for publishers. Setting and validating the adapter data is the reponsibility
 * of the concrete class.
 */
abstract class AbstractTask implements AmqpTaskInterface
{	
	/**
	 * Method signature for adapterMethod
	 * @var array
	 */
	protected $adapterData = array();

	/**
	 * @var AmqpProfileInterface
	 */
	protected $profile = null;
	
	/**
     * @param   AmqpProfileInterface	$profile
	 * @return	AbstractTask
	 */
	public function __construct(AmqpProfileInterface $prof, array $data = null)
	{

        $this->setProfile($prof);
		
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
	 * @param	array
	 * @return	null
	 */
	protected function setAdapterData(array $data)
	{
		$this->adapterData = $data;
	}
}
