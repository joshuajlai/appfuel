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
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class MysqliAdapter implements AdapterInterface
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
	 * @return mysqli
	 */
	public function createHandle()
	{
		return mysqli_init();
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
	 * Connect to the database using the ConnectionDetail
	 * @return bool
	 */
	public function connect()
	{
		if ($this->isConnected()) {
			return true;
		}

		$handle = $this->getHandle();
		if (! $handle instanceof Mysqli) {
			throw new Exception('connect failed: no mysqli handle created');
		}
		
		$detail = $this->getConnectionDetail();
		
		$this->isConnected = @mysqli_real_connect(
			$handle,
			$detail->getHost(),
			$detail->getUserName(),
			$detail->getPassword(),
			$detail->getDbName(),
			$detail->getPort(),
			$detail->getSocket()
		);
	
		if (! $this->isConnected) {
			$errNbr  = mysqli_connect_errno();
			$errCode = mysqli_connect_error();
			$error   = $this->createError($errNbr, $errCode);
			$this->setError($error);
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
	 * @return 
	 */
	public function clearHandle()
	{
		$this->handle = null;	
	}
}
