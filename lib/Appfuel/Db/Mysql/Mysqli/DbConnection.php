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
	Appfuel\Error\ErrorItem,
	Appfuel\Db\DbConnectionInterface,
	Appfuel\Db\ConnectionDetailInterface;

/**
 * The primary responsibilty of the DbConnection is encapsulate vendor specific
 * details for connecting and disconnecting from the database server as well 
 * as creating a DbQuery object used to issue database queries. Opening and 
 * closing as well as finding errors are done throug delegation of the native
 * mysqi object which is created in the constructor
 */
class DbConnection implements DbConnectionInterface
{
	/**
	 * Value object used to hold the connection details
	 * @var	ConnectionDetail
	 */
	protected $connDetail = null;

	/**
	 * Mysqli object used to interact with the database
	 * @var	mysqli
	 */	
	protected $driver = null;

	/**
	 * Flag used to determine if a connection to the database has been 
	 * established. This connection is always done through mysqli_real_connect
	 * @var bool
	 */
	protected $isConnected = false;

	/**
	 * Connection error 
	 * @var ErrorItem
	 */
	protected $error;

	/**
	 * Flag used to determine if an error has occured
	 * @var bool
	 */
	protected $isError = false;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(ConnectionDetailInterface $detail)
	{
		$this->connDetail = $detail;
	}

	/**
	 * @throws	RunTimeException	
	 * @return	DbConnection
	 */
	public function loadDriver()
	{
		$driver = mysqli_init();
		if (! ($driver instanceof MysqliDriver)) {
			throw new RunTimeException("Could not use mysqli_init()");
		}
		$this->driver = $driver;
		return $this;
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
	 * Connect to the database using the ConnectionDetail. I surpress errors 
	 * because php is only going to raise an exception which gives php's error 
	 * code and not mysql. We can provide better error handling by setting 
	 * the drivers error text and code.
	 *
	 * @return	bool 
	 */
	public function connect()
	{
		if ($this->isConnected()) {
			return true;
		}

		if (! $this->isDriver()) {
			$this->loadDriver();
		}
		
		$driver = $this->getDriver();
		$detail = $this->getConnectionDetail();
		
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
			$this->isConnected = false;
			return false;
		}

		$this->isConnected = true;
		return true; 
	}

	/**
	 * The DbQuery encapsulates Mysqli implementation details for executing
	 * sql queries.
	 *
	 * @return	DbQuery
	 */
	public function createDbQuery()
	{
		return new DbQuery($this->getDriver());
	}

	/**
	 * Close the database connection and set the isConnected flag to false
	 *
	 * @return	bool
	 */
	public function close()
	{
		if (! $this->isDriver() || ! $this->isConnected()) {
			return true;
		}
		
		$driver = $this->getDriver();
		$result = $driver->close();
		if (false === $result) {
			return false;
		}

		$this->isConnected = false;
		return true;
	}

	/**
	 * Flag used to detemine if there is an open connection to the database.
	 *
	 * @return	bool
	 */
	public function isConnected()
	{
		return $this->isConnected;
	}

	/**
	 * @return	ErrorItem | null 
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return $this->error instanceof ErrorItem;
	}

	/**
	 * @param	string	$code
	 * @param	string	$text
	 * @return	null
	 */
	protected function setError($code, $text)
	{
		$this->error = new ErrorItem($text, $code);
	}
}
