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
	AmqpMessage,
	AmqpChannel as AmqpChannelAdapter,
	Appfuel\Framework\MsgBroker\Amqp\AmqpTaskInterface,
	Appfuel\Framework\MsgBroker\Amqp\TaskHandlerInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpChannelInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpProfileInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectorInterface,
	Appfuel\Framework\MsgBroker\Amqp\AmqpConnectionInterface;

/**
 * Adapter for the AMQPChannel
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
	 * @var	AmqpConnectionInterface
	 */
	protected $connection = null;

	/**
	 * The channel adapter is created by the connection which handles 
	 * delaring, binding, consuming and publishing
	 * @var	\AMQPChannel
	 */
	protected $channelAdapter = null;

	/**
	 * @return	AmqpConnector
	 */
	public function __construct($conn, AmqpTaskInterface $task)
	{
		$this->setTask($task);
		$this->setConnection($conn);
	}
	
	/**
	 * @return	ConsumerInterface
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
	 * @return	AmqpConnectionInterface
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
		if ($this->connection instanceof AmqpConnectionInterface) {
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
	 * @return	null
	 */
	public function initialize()
	{
		$conn = $this->getConnection();
		if (! $conn->isConnected()) {
			$conn->connect();
		}

		$this->channelAdapter = $conn->createChannelAdapter();
	}

	/**
	 * Setting up the channel involves declaring the exchange, queue and 
	 * binding the exchange to the queue
	 *
	 * @return	null
	 */
	public function setupChannel()
	{
		$adapter = $this->getChannelAdapter();
		if (! $adapter) {
			$err  = "Must initialize handler before channel can be setup ";
			$err .= "be registered";
			throw new Exception($err);
		}

        $this->declareExchange($adapter);
        $this->declareQueue($adapter);
        $this->bindQueue($adapter);
	}

	/**
	 * Register the main consumer or publish method. This is the channel method
	 * used to preform the given task. Consumer is basic_consume and Publish
	 * is basic_publish
	 */
	public function registerAdapterMethod()
	{
		$task	 = $this->getTask();
		$adapter = $this->getChannelAdapter();
		if (! $adapter) {
			$err  = "Must initialize handler before channel method can ";
			$err .= "be registered";
			throw new Exception($err);
		}
        return call_user_func_array(
            array($adapter, $task->getAdapterMethod()),
            $task->getAdapterValues()
        );
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
    public function declareExchange(AmqpChannelAdapter $adapter)
    {
		$values = $this->getTask()
					   ->getExchangeValues();	
        
		return call_user_func_array(
            array($adapter, 'exchange_declare'),
            $values
        );
    }

    /**
     * @return  
     */
    public function declareQueue(AmqpChannelAdapter $adapter)
    {
		$values = $this->getTask()
					   ->getQueueValues();	
    
		return call_user_func_array(
            array($this->channelAdapter, 'queue_declare'),
            $values
        ); 
    }

    /**
     * @return  
     */
    public function bindQueue(AmqpChannelAdapter $adapter)
    {
		$values = $this->getTask()
					   ->getBindValues();	
        
		return call_user_func_array(
            array($this->channelAdapter, 'queue_bind'),
            $values
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
	 * 1) Having an AmqpConnectionInterface 
	 * 2) Having an AmqpConnector from a AmqpConnection is made
	 * 3) Having an associative array of parameters to create an
	 *	  AmqpConnector that will be used to create AmqpConnection
	 *
	 * @param	mixed	$conn 
	 * @return	null
	 */
	protected function setConnection($conn)
	{
		if ($conn instanceof AmqpConnectionInterface) {
			$this->connector  = $conn->getConnector();
			$this->connection = $conn;
		}
		else if ($conn instanceof AmqpConnectorInterface) {
			$this->connector  = $conn;
			$this->connection = $this->createConnection($conn); 
		}
		else if (is_array($conn)) {
			$this->connection = $this->createConnection($conn);
			$this->connector  = $this->connection->getConnector();
		}
		else {
			$err = "parameter must implement AmqpConnectorInterface | ";
			$err = "AmqpConnectionInterface"; 
			throw new Exception($err); 
		}
	}

	/**
	 * @param	AmqpConnectorInterface $conn
	 * @return	AmqpConnection
	 */
	protected function createConnection($conn)
	{	
		if (is_array($conn)) {
			$conn = new AmqpConnector($conn);
		}
		return new AmqpConnection($conn);
	}
}
