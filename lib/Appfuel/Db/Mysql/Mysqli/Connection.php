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

use mysqli as mysqli_handle,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Adapter\AdapterInterface,
	Appfuel\Framework\Db\Adapter\ErrorInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Handles only the connection to the database. The connection has 
 * states:	
 *			never connected 
 *			connected
 *			failed to connect
 *			closed 
 *			failed to close
 *
 */
class Connection
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
	protected $status = 'never connected';


	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(ConnectionDetailInterface $detail, 
								mysqli_handle $handle)
	{
		$this->connDetail = $detail;
		$this->handle     = $handle;
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
		return $this->handle instanceof mysqli_handle;
	}

	/**
	 * @return	mysqli_stmt
	 */
	public function createStmtHandle()
	{
		if (! $this->isConnected()) {
			$this->setError(
				'AF_CONN_ERR',
				'connect failure: must be connected to create stmt handle'
			); 
			return false;
		}

		return $this->getHandle()
					->stmt_init();
	}

	/**
	 * Connect to the database using the ConnectionDetail
	 * @return bool
	 */
	public function connect()
	{
		$status = $this->getStatus();
		if (! $this->isHandle() || 'closed' === $status) {
			$this->setError(
				'AF_CONN_ERR',
				'connect failure: handle has (closed | failed)'
			); 
			return false;

		}
		
		if ($this->isConnected()) {
			return true;
		}

		$handle = $this->getHandle();
		$detail = $this->getConnectionDetail();
		
		$isConnected = @mysqli_real_connect(
			$handle,
			$detail->getHost(),
			$detail->getUserName(),
			$detail->getPassword(),
			$detail->getDbName(),
			$detail->getPort(),
			$detail->getSocket()
		);
	
		if (! $isConnected) {
			$this->setError($handle->connect_errno, $handle->connect_error); 
			$this->isConnected  = false;
			$this->status       = 'connection failure';
			return false;
		}

		$this->status		= 'connected';
		$this->isConnected  = true;
		return true; 
	}

	/**
	 * @return	bool
	 */
	public function close()
	{
		$status = $this->getStatus();
		if (! $this->isHandle() || 'closed' === $status) {
			return true;
		}
		
		$handle = $this->getHandle();
		if (! $handle->close()) {
			$this->setError($handle->connect_errno, $handle->connect_error); 
			$this->status = 'failed to close';
			return false;
		}

		$this->status      = 'closed';
		$this->isConnected = false;
		$this->handle      = null;
		unset($handle);

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
	 * @return	ConnectionDetail
	 */
	protected function getConnectionDetail()
	{
		return $this->connDetail;
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

}
