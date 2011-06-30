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
namespace Appfuel\Db\Mysql\Adapter;

use Mysqli,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Adapter\AdapterInterface,
	Appfuel\Framework\Db\Adapter\ErrorInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Handles all low level routines that deal with the server
 */
class Server
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
	protected $handle = null;

	/**
	 * Flag used to determine if a connection to the database has been 
	 * established. This connection is always done through mysqli_real_connect
	 * @var bool
	 */
	protected $isConnected = false;

	/**
	 * Error object used to describe connection error
	 * @var Error
	 */
	protected $connError = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(ConnectionDetailInterface $detail)
	{
		$this->connDetail = $detail;
	}

	/**
	 * @return	ConnectionDetail
	 */
	public function getConnectionDetail()
	{
		return $this->connDetail;
	}

	/**
	 * @return	MysqliAdapter
	 */
	public function initialize()
	{
		if ($this->isHandle()) {
			return $this;
		}

		$this->setHandle($this->createHandle());
		return $this;
	}

	/**
	 * @return mysqli
	 */
	public function createHandle()
	{
		return mysqli_init();
	}

	/**
	 * @return mysqli_stmt
	 */
	public function createStmtHandle()
	{
		if (! $this->isHandle()) {
			return null;
		}

		return $this->getHandle()
					->stmt_init();
	}

	/**
	 * @param	Mysqli	$handle
	 * @return	Adapter
	 */
	public function setHandle(Mysqli $handle)
	{
		$this->handle = $handle;
		return $this;
	}

	/**
	 * @return	Mysqli
	 */
	public function getHandle()
	{
		return $this->handle;
	}

	/**
	 * @return	bool
	 */
	public function isHandle()
	{
		return $this->handle instanceof Mysqli;
	}
	
	/**
	 * @return	Server
	 */
	public function clearHandle()
	{
		if (! $this->isHandle()) {
			return $this;
		}

		if ($this->isConnected()) {
			$result = $this->close();
			if (! $result) {
				throw new Exception("Could not close the connection");
			}
		}

		$this->handle = null;
		return $this;
	}

	/**
	 * Connect to the database using the ConnectionDetail
	 * @return bool
	 */
	public function connect()
	{
		$hdl = $this->getHandle();
		if (! $hdl instanceof Mysqli) {
			throw new Exception('connect failed: no mysqli handle created');
		}
		
		$detail = $this->getConnectionDetail();
		
		$isConnected = @mysqli_real_connect(
			$hdl,
			$detail->getHost(),
			$detail->getUserName(),
			$detail->getPassword(),
			$detail->getDbName(),
			$detail->getPort(),
			$detail->getSocket()
		);
	
		if (! $isConnected) {
			$this->setConnectionError($hdl->connect_errno, $hdl->connect_error);
			$this->isConnected = false;
			return false;
		}

		$this->isConnected = true;
		return true;
	}

	/**
	 * @return	bool
	 */
	public function close()
	{
		$this->isConnected = false;
		if (! $this->isHandle()) {
			return true;
		}
		$hdl = $this->getHandle();

		if (! $hdl->close()) {
			$this->isConnected = true;
			$this->setConnectionError($hdl->connect_errno, $hdl->connect_error);
			return false;
		}

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
	 * @return Error | null
	 */
	public function getConnectionError()
	{
		return $this->connError;
	}

	/**
	 * A string that represents the Mysql client library version
	 *
	 * @return	string
	 */
	public function getClientInfo()
	{
		return mysqli_get_client_info();
	}

	/**
	 * A number that represents the MySQL client library format:
	 * main_version*10000 + minor_version*100 + sub_version
	 * example) 4.1.0 is returned as 40100
	 *
	 * @return	int
	 */
	public function getClientVersion()
	{
		if (! $this->isHandle()) {
			return null;
		}

		return $this->getHandle()
					->client_version;
	}

	/**
	 * Returns client per-process statistics. This is only available with
	 * the mysqlnd driver compiled into php
	 *
	 * @return array | false on failure | null not initialized
	 */
	public function getClientStats()
	{
		if (! $this->isHandle()) {
			return null;
		}

		return mysqli_get_client_stats();
	}

	/**
	 * Returns statistics about the client connection. This is only available \
	 * with the mysqlnd driver compiled into php
	 *
	 * @return array | false on failure | null not initialized
	 */
	public function getConnectionStats()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		$handle = $this->getHandle();
		return mysqli_get_connection_stats($handle);
	}

	/**
	 * Returns a string representing the type of connection being used. 
	 * 
	 * @return	string | null when not connected
	 */
	public function getHostInfo()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->host_info;
	}

	/**
	 * Returns the version of the MySQL protocol used
	 * 
	 * @return	int | null	when not connected
	 */
	public function getProtocolVersion()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->protocol_version;
	}

	/**
	 * Returns the version of the mysql server
	 * 
	 * @return	string | null	when not connected
	 */
	public function getServerInfo()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->server_info;
	}

	/**
	 * Returns the version of the mysql server as an integer
	 * 
	 * @return	int | null	when not connected
	 */
	public function getServerVersion()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->server_version;
	}

	/**
	 * Returns the current system status
	 *
	 * @return string
	 */
	public function getServerStatus()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->stat();
	}

	/**
	 * The default character set for the current connection
	 *
	 * @return	string | null when not connected
	 */
	public function getDefaultCharset()
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return null;
		}

		return $this->getHandle()
					->character_set_name();
	}

	/**
	 * @param	string	$charset
	 * @reutrn	bool
	 */
	public function setDefaultCharset($charset)
	{
		if (! $this->isHandle() || ! $this->isConnected()) {
			return false;
		}

		return $this->getHandle()
					->set_charset($charset);
	}

	/**
	 * @param	int		$errNbr
	 * @param	int		$errText
	 * @return	null
	 */
	protected function setConnectionError($errNbr, $errText)
	{
		$this->connError = new Error($errNbr, $errText);
	}
}
