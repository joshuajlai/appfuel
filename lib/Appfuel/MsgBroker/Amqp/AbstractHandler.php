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
	AmqpChannel		as AmqpChannelAdapter,
	AmqpConnection  as AmqpConnectionAdapter,
	Appfuel\Framework\MsgBroker\Amqp\AmqpTaskInterface,
	Appfuel\Framework\MsgBroker\Amqp\TaskHandlerInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface;

/**
 * This class holds the common logic needed to handle either publishing or 
 * consuming messages in rabbitmq. Both consumers and publisher implement
 * the AmqpTaskInterface and are know as tasks. The repsonsibility is to
 * assign the connector and use it to create an amqp connection. That 
 * connection adapter is used to create a channel for which we use a task 
 * profile to declare the exchange, declare the queue and bind them. 
 * We also can register a shutdown function for cleanup. 
 */
abstract class AbstractHandler implements TaskHandlerInterface
{
	/**
	 * There are two kinds of tasks a Consumer or Publish only on or the
	 * other is allowed
	 *
	 * @var	TaskInterface
	 */
	protected $task = null;

	/**
	 * Value object used to descibe the connection details
	 * @var	AmqpConnectorInterface
	 */
	protected $connector = null;
	
	/**
	 * Conection controls the amqp connection and creates the channel 
	 * object.
	 * @var	AmqpConnectionAdapter
	 */
	protected $connection = null;

	/**
	 * The channel adapter is created by the connection which handles 
	 * delaring, binding, consuming and publishing
	 * @var	\AMQPChannel
	 */
	protected $channelAdapter = null;

	/**
	 * @param	mixed	array | AmqpConnectorInterface $conn
	 * @param	AmqpTaskInterface $task
	 * @return	AbstractHandler
	 */
	public function __construct($conn, AmqpTaskInterface $task)
	{
		$this->setTask($task);
		$this->setConnector($conn);
	}
	
	/**
	 * @return	AmqpTaskInterface
	 */
	public function getTask()
	{
		return $this->task;
	}

	/**
	 * @return	AmqpChannelAdapter
	 */
	public function getChannelAdapter()
	{
		return $this->channelAdapter;
	}

	/**
	 * @return	AmqpConnectorInterface
	 */
	public function getConnector()
	{
		return $this->connector;
	}

	/**
	 * @return	AmqpConnectionAdapter
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * @return	bool
	 */
	public function isConnection()
	{
		if ($this->connection instanceof AmqpConnectionAdapter) {
			return true;
		}

		return false;
	}

	/**
	 * @return	bool
	 */
	public function isChannelAdapter()
	{
		if ($this->channelAdapter instanceof AmqpChannelAdapter) {
			return true;
		}

		return false;
	}

	/**
	 * Connect and create the channel adapter
	 *
	 * @return	null
	 */
	public function initialize()
	{
		if (! $this->isConnection()) {
			$connector  = $this->getConnector();
			$connection = $this->createConnection($connector); 
		}
		else {
			$connection = $this->getConnection();
		}

		$this->channelAdapter = $connection->channel();
	}

	/**
	 * Setting up the channel involves declaring the exchange, queue and 
	 * binding the exchange to the queue
	 *
	 * @return	null
	 */
	public function setupChannel(AmqpChannelAdapter $adapter,
                                    AmqpTaskInterface $task)
	{
        $this->declareExchange($adapter, $task);
        $this->declareQueue($adapter, $task);
        $this->bindQueue($adapter, $task);
	}

	/**
	 * @return	null
	 */
	public function registerShutDown()
	{
		register_shutdown_function(array($this, 'shutDown'));
	}

    /**
     * @return  
     */
    public function declareExchange(AmqpChannelAdapter $adapter, 
									AmqpTaskInterface $task)
    {
		return call_user_func_array(
            array($adapter, 'exchange_declare'),
			$task->getExchangeValues()
        );
    }

    /**
     * @return  
     */
    public function declareQueue(AmqpChannelAdapter $adapter,
								 AmqpTaskInterface $task)
    {
    
		return call_user_func_array(
            array($adapter, 'queue_declare'),
			$task->getQueueValues()
        ); 
    }

    /**
     * @return  
     */
    public function bindQueue(AmqpChannelAdapter $adapter,
							  AmqpTaskInterface $task)
    {
		return call_user_func_array(
            array($adapter, 'queue_bind'),
            $task->getBindValues()
        ); 
    }

	/**
	 * Close the channel and the connection
	 *
	 * @return	null
	 */
	public function shutDown()
	{
		if ($this->isChannelAdapter()) {
			$this->getChannelAdapter()
				 ->close();
		}

		if ($this->isConnection()) {
			$this->getConnection()
				 ->close();
		}
	}

	/**
	 * @param	AmqpTaskInterface $task
	 * @return	null
	 */
	protected function setTask(AmqpTaskInterface $task)
	{
		$this->task = $task;
	}

	/**
	 * There are three ways to set the connection 
	 * 1) Having an AmqpConnector from a AmqpConnection is made
	 * 2) Having an associative array of parameters to create an
	 *	  AmqpConnector that will be used to create AmqpConnection
	 *
	 * @param	mixed	$conn 
	 * @return	null
	 */
	protected function setConnector($conn)
	{
		if ($conn instanceof AmqpConnectorInterface) {
			$this->connector  = $conn;
		}
		else if (is_array($conn)) {
			$this->connector = $this->createConnector($conn);
		}
		else {
			$err = "parameter must implement AmqpConnectorInterface | ";
			$err = "or associative array of connection parameters "; 
			throw new Exception($err); 
		}
	}

	/**
	 * @param	array  $conn
	 * @return	AmqpConnector
	 */
	protected function createConnector(array $conn)
	{	
		return new AmqpConnector($conn);
	}

	/**
	 * This will create the phplib AMQPConnection which will connect to
	 * rabbit right array
	 *
	 * @param	AmqpConnectorInterface $conn
	 * @return	\AMQPConnection alias as AmqpConnectionAdapter
	 */
	protected function createConnection(AmqpConnectorInterface $conn)
	{
        return new AmqpConnectionAdapter(
            $conn->getHost(),
            $conn->getPort(),
            $conn->getUser(),
            $conn->getPassword(),
            $conn->getVirtualHost()
        );
	}
}
