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

use AmqpMessage,
	Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\PublisherTaskInterface;

/**
 * Holds a profile and manages setting for basic_publish function of the 
 * amqp library
 */
class PublisherTask extends AbstractTask implements PublisherTaskInterface
{
    /**
     * Datastructure to hold the info required for method call to basic_publish
     * @var array
     */
    protected $adapterData = array(
		'message'		=> '',
        'exchange'		=> '',
        'route-key'		=> '',
        'mandatory'		=> false,
        'immediate'		=> false,
        'ticket'        => null
    );

	/**
     * @param   array   $data
	 * @return	ConsumerDetail
	 */
	public function __construct(AmqpProfileInterface $prof, array $data = null)
	{
		parent::__construct($prof, $data);
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	public function setMessage($msg)
	{
		$this->adapterData['message'] = new AMQPMessage($msg);
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	public function setRouteKey($key)
	{
		if (! is_string($key)) {
			throw new Exception("Routing key must be a string");
		}

		$this->adapterData['route-key'] = $key;
		return $this;
	}

	/**
	 * Data holds the parameters used in basic_publish for the channel adapter
	 *
	 * @throws	Appfuel\Framework\Exception
	 * @param	array	$data 
	 * @return	null
	 */
	protected function setAdapterData(array $data)
	{
		$profile = $this->getProfile();
		$this->adapterData['message'] = '';
		if (isset($data['message']) && 
			$data['message'] instanceof AMQPMessage) {
			$this->adapterData['message'] = $data['message'];
		}

		$exchangeName = $profile->getExchangeName();
		if (! isset($data['exchange']) || ! is_string($data['exchange'])) {
			$data['exchange'] = $exchangeName;
		}
		$this->adapterData['exchange'] = trim($data['exchange']);
		
		if (isset($data['route-key']) && is_string($data['route-key'])) {
			$this->adapterData['route-key'] = $data['route-key'];	
		}

		if (isset($data['mandatory']) && true === $data['mandatory']) {
			$this->adapterData['mandatory'] = true;	
		}

		if (isset($data['immediate']) && true === $data['immediate']) {
			$this->adapterData['immediate'] = true;	
		}

        if (isset($data['ticket']) && is_int($data['ticket'])) {
            $this->adapterData['ticket'] = $data['ticket'];
        }

	}
}
