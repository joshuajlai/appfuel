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
	AmqpConnection as AmqpConnectAdapter,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface;

/**
 * Adapter for the AMQPConnection
 */
class AmqpConnection implements AmqpConnectionInterface
{
	/**
	 * Holds all the connection paramters
	 * @var	AmqpConnectorInterface
	 */
	protected $connector = null;

	/**
	 * @var	\AmqpConnection
	 */
	protected $adapter = null;

	/**
	 * @return	AmqpConnector
	 */
	public function __construct(AmqpConnectorInterface $connector)
	{
		$this->connector = $connector;
	}
	
	/**
	 * @return	AmqpConnectorInterface
	 */
	public function getConnector()
	{
		return $this->connector;
	}

	/**
	 * @return	AmqpChannel
	 */
	public function connect()
	{
		$adapter = $this->createAdapter();
		$this->setAdapter($adapter);
	}

	/**
	 * @return	true
	 */
	public function close()
	{
		if (! $this->isConnected()) {
			return true;
		}

		$adapter = $this->getAdapter();
		$adapter->close();
		$this->adapter = null;
		return true;
	}

	/**
	 * @return	bool
	 */
	public function isConnected()
	{
		return $this->adapter instanceof AmqpConnectAdapter;
	}

	/**
	 * @return	\AmqpChannel | false on error
	 */
	public function createChannelAdapter()
	{
		if (! $this->isConnected()) {
			return null;
		}

		return $this->getAdapter()
					->channel();
	}
	
	/**
	 * @return	AmqpConnectAdapter
	 */
	protected function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @param	AmqpConnectAdapter
	 * @return	null
	 */
	protected function setAdapter(AmqpConnectAdapter $adapter)
	{
		$this->adapter = $adapter;
		return null;
	}

	/**
	 * @return	AmqpConnectAdapter
	 */
	protected function createAdapter()
	{
		$connector = $this->getConnector();
		return new AmqpConnectAdapter(
			$connector->getHost(),
			$connector->getPort(),
			$connector->getUser(),
			$connector->getPassword(),
			$connector->getVirtualHost()
		);
	}
}
