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
namespace Appfuel\App\Init;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Registry,
	Appfuel\MsgBroker\Amqp\AmqpManager,
	Appfuel\Framework\App\Init\TaskInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpFactoryInterface;

/**
 * Use the db initializer to intialize the database system
 */
class AmqpTask implements TaskInterface
{
    /**  
	 * @return	null
     */
	public function init()
	{
		/* 
		 * used to create connectors profiles etc ..
		 */
		$defaultFactory = 'Appfuel\MsgBroker\AmqpFactory';
		$defaultKey	= Registry::get('amqp-default-connector', 'local');
		$connectors = Registry::get('amqp-connectors', array());
	
		$fclass = Registry::get('amqp-factory', $defaultFactory);
		if (empty($fclass) || ! is_string($fclass)) {
			throw new Exception("invalid factory class given");
		}

		$factory = new $fclass();
		if (! ($factory instanceof AmqpFactoryInterface)) {
			$err = "factory object must implement AmqpFactoryInterface";
			throw new Exception($err);
		}

		AmqpManager::setFactory($factory);
		AmqpManager::setDefaultConnectorKey($defaultKey);
		foreach ($connectors as $key) {
			$connector = $factory->createConnector($key);
			AmqpManager::addConnector($key, $connector);
		}	
	}
}
