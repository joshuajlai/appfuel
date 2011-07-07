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
	protected $errNbr = 0;

	/**
	 * Connection error text
	 * @var string
	 */	
	protected $errTxt = null;

	/**
	 * Textual information about the state of the connection
	 * @var string
	 */
	protected $status = 'never connected';


	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(ConnectionDetailInterface $detail, mysqli $hdl)
	{
		$this->connDetail = $detail;
		$this->handle     = $hdl;
	}

	/**
	 * @return	Mysqli
	 */
	public function getHandle()
	{
		return $this->handle;
	}

	/**
	 * @return	mysqli_stmt
	 */
	public function createStmtHandle()
	{
		$status = $this->getStatus();
		if (! $this->isConnected() && 'connected' === $status) {
			$this->errNbr = 'AF_CONN_ERR';
			$this->errTxt = 'connect failure: must be connected to create ' .
							'mysqli_stmt';
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
			$this->errNbr = 'AF_CONN_ERR';
			$this->errTxt = 'connect failure: handle has (closed | failed)';
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
			$this->errNbr		= $handle->connect_errno;
			$this->errTxt		= $handle->connect_error;
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
			$this->errNbr = $handle->connect_errno;
			$this->errTxt = $handle->connect_error;
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
	public function getErrorNbr()
	{
		return $this->errNbr;
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
		if ($this->errNbr > 0) {
			return true;
		}

		return false;
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
	 * @return	bool
	 */
	protected function isHandle()
	{
		return $this->handle instanceof mysqli;
	}
}
