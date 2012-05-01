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
namespace Appfuel\Db\Mysql\Mysqli;

use RunTimeException,
	mysqli as MysqliDriver,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Request\RequestInterface,
	Appfuel\Framework\Db\Request\MultiQueryRquest,
	Appfuel\Framework\Db\Request\PreparedRequest,
	Appfuel\Framework\Db\Adapter\AdapterInterface,
	Appfuel\Framework\Db\Adapter\ErrorInterface,
	Appfuel\Framework\Db\Connection\ConnectionInterface,
	Appfuel\Db\ConnectionDetailInterface;

/**
 * Handles only the connection to the database. The connection has following
 * states:	
 *			uninitialized
 *			initialized 
 *			connected
 *			connection failed
 *			connection closed 
 *			connection closed failed
 *
 */
class Connection implements ConnectionInterface
{
	/**
	 * Connection detail holds the info necessary to connect to the database
	 * via the mysqli handle
	 * @var	ConnectionDetail
	 */
	protected $connDetail = null;

	/**
	 * Mysqli object used to interact with the database
	 * @var	Mysqli
	 */	
	protected $driver = null;

	/**
	 * Flag used to determine if a connection to the database has been 
	 * established. This connection is always done through mysqli_real_connect
	 * @var bool
	 */
	protected $isConnected = false;

	/**
	 * Connection error number used by mysqli
	 * @var int
	 */
	protected $errCode = 0;

	/**
	 * Connection error text
	 * @var string
	 */	
	protected $errTxt = null;
	
	/**
	 * Flag used to determine if an error has occured
	 * @var bool
	 */
	protected $isError = false;

	/**
	 * Textual information about the state of the connection
	 * @var string
	 */
	protected $status = 'initialized';


	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(ConnectionDetailInterface $detail)
	{
		$this->connDetail = $detail;
		$driver = mysqli_init();
		if (! $driver instanceof MysqliDriver) {
			throw new RunTimeException("Could not use mysqli_init()");
		}
		$this->driver = $driver;


	}

	/**
	 * @return	ConnectionDetail
	 */
	public function getConnectionDetail()
	{
		return $this->connDetail;
	}

	/**
	 * @return	Mysqli
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @return	bool
	 */
	public function isDriver()
	{
		return $this->driver instanceof MysqliDriver;
	}

	/**
	 * @return	mysqli_stmt
	 */
	public function createStmtDriver()
	{
		if (! $this->isConnected()) {
			$this->setError(
				'AF_CONN_ERR',
				'connect failure: must be connected to create stmt handle'
			); 
			return false;
		}

		return $this->getDriver()
					->stmt_init();
	}

	/**
	 * Connect to the database using the ConnectionDetail
	 * @return bool
	 */
	public function connect()
	{
		if ($this->isConnected()) {
			return true;
		}

		if (! $this->isDriver()) {
			$this->setError(
				'AF_CONN_ERR',
				'connect failure: must have an intialized mysqli driver'
			); 
			return false;
		}
		
		$driver = $this->getDriver();
		$detail = $this->getConnectionDetail();
		
		/* we surpress errors because php is only going to raise an exception 
		 * which gives php's error code and not mysql. To avoid this we 
		 * interpret the return value an look at the drivers error code
		 */
		$isConnected = @mysqli_real_connect(
			$driver,
			$detail->getHost(),
			$detail->getUserName(),
			$detail->getPassword(),
			$detail->getDbName(),
			$detail->getPort(),
			$detail->getSocket()
		);
	
		if (! $isConnected) {
			$this->setError($driver->connect_errno, $driver->connect_error); 
			$this->isConnected  = false;
			$this->setStatus('connection failed');
			return false;
		}

		$this->setStatus('connected');
		$this->isConnected  = true;
		return true; 
	}

	/**
	 * Execute a given request by creating the adapter corresponding to the
	 * request type. Adapters are designed to handle the interfaces for that
	 * particular request. 
	 *
	 * @return	DbResponse
	 */
	public function createAdapter($code)
	{
		if (empty($code) || ! is_string($code)) {
			return false;
		}

		$driver = $this->getDriver();
		switch ($code) {
			case 'multiquery': 
				$adapter = new MultiQuery\Adapter($driver);
				break;
			case 'prepared':
				$adapter = new PreparedStmt\Adapter($driver);
				break;
			default:
				$adapter = new Query\Adapter($driver); 
		}
		return $adapter;
	}

	/**
	 * @return	bool
	 */
	public function close()
	{
		$status = $this->getStatus();
		if (! $this->isDriver()) {
			return true;
		}
		
		if (! $this->driver->close()) {
			$this->setError($driver->connect_errno, $driver->connect_error); 
			$this->setStatus('connection closed failed');
			return false;
		}

		$this->setStatus('closed');
		$this->isConnected = false;
		$this->driver      = null;
		return true;
	}

	/**
	 * @return	bool
	 */
	public function isConnected()
	{
		return $this->isConnected;
	}

	/**
	 * @return	int
	 */
	public function getErrorCode()
	{
		return $this->errCode;
	}

	/**
	 * @return	string
	 */
	public function getErrorText()
	{
		return $this->errTxt;
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return $this->isError;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param	string	$code
	 * @param	string	$text
	 * @return	null
	 */
	protected function setError($code, $text)
	{
		$this->errCode = $code;
		$this->errTxt  = $text;
		$this->isError = true;
	}

	/**
	 * @param	string	$name
	 * @return	Connection
	 */
	protected function setStatus($name)
	{
		$this->status = $name;
		return $this;
	}
}
