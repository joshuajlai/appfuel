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

use	Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\ConsumerInterface;

/**
 * 
 */
class AmqpManager
{
    /**
	 * When nothing is specified the connector key used will be this one
     * @var string
     */
    static protected $defaultConnector = null;

	/**
	 * Holds a list of connectors that can be used to connect to rabbitmq
	 * @var array
	 */
	static protected $connectors = array();

	/**
	 * Factory object used to create connectors, profiles, publishers or
	 * consumers
	 * @var	AmqpFactoryInterface
	 */
	static protected $factory = null;

    /**
     * @param   string  $key    used to identify the connector
     * @return  false | when not found
     */
    static public function getConnector($key = null)
    {  
        if (null === $key) {
            $key = self::getDefaultConnectorKey();
        }

        if (! self::isConnector($key)) {
            return false;
        }

        return self::$connectors[$key];
    }

    /**
     * Flag used to determine if a valid connector is available for the 
     * given key
     *
     * @param   string  $key
     * @return  bool
     */
    static public function isConnector($key)
    {
        if (empty($key)         ||
            ! is_string($key)   ||
            ! isset(self::$connectors[$key]) ||
            ! self::$connectors[$key] instanceof AmqpConnectorInterface) {
            return false;
        }

        return true;
    }

	/**
	 * @param	string	$key
	 * @param	mixed	array | AmqpConnectorInterface
	 * @return	null
	 */
	static public function addConnector($key, $data)
	{
		if (empty($key) || ! is_string($key)) {
			throw new Exception("connector key must be a non empty string");
		}

		if (is_array($data)) {
			$connector = new AmqpConnector($data);
		}
		else if ($data instanceof AmqpConnectorInterface) {
			$connector = $data;
		}
		else {
			$err = "connector must be an array or AmqpConnectorInterface";
			throw new Exception($err);
		}
	}

    /**
     * @return  string
     */
    static public function getDefaultConnectorKey()
    {  
        return self::$defaultConnector;
    }

    /**
     * @param   string  $name   name of the default connection group
     * @return  null
     */
    static public function setDefaultConnectorKey($name)
    {  
        if (empty($name) || ! is_string($name)) {
            throw new Exception("Invalid name must be non empty string");
        }

        self::$defaultConnector = $name;
    }

	/**
	 * @param	AmqpFactoryInterface	$factory
	 * @return	null
	 */
	static public function setFactory(AmqpFactoryInterface $factory)
	{
		self::$factory = $factory;
	}

	/**
	 * @return	AmqpFactoryInterface
	 */
	static public function getFactory()
	{
		return self::$factory;
	}
}
