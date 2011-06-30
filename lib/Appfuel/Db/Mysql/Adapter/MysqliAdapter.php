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
	Appfuel\Framework\Db\Adapter\CallbackErrorInterface,
	Appfuel\Framework\Db\Connection\ConnectionDetailInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class MysqliAdapter implements AdapterInterface
{
	/**
	 * Handles all low level details pertaining to the server, this includes
	 * connecting, and getting the handle
	 * @var Server
	 */
	protected $server = null;

	/**
	 * Error value object containing the last know error
	 * @var ErrorInterface
	 */
	protected $error = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(Server $server)
	{
		$server->initialize();
		$this->server = $server;
	}

	/**
	 * @return	Server
	 */
	public function getServer()
	{
		return $this->server;
	}

	/**
	 * @return	ConnectionDetailInterface
	 */
	public function getConnectionDetail()
	{
		return $this->getServer()
					->getConnectionDetail();		
	}

	/**
	 * @return bool
	 */
	public function isConnected()
	{
		return $this->getServer()
					->isConnected();
	}

	/**
	 * Establish a connection to the database using the connection detail
	 * located in the server
	 *
	 * @return bool
	 */
	public function connect()
	{
		$server = $this->getServer();
		if ($server->isConnected()) {
			return true;
		}

		if (! $server->connect()) {
			$this->assignError($server->getConnectionError());
			return false;		
		}

		return true;
	}

	/**
	 * @param	string	$sql
	 * @param	array	$values 
	 * @param	bool	$isBuffered
	 * @param	mixed	$filter
	 * @return	DbResponse
	 */
	public function executePreparedStmt($sql, 
										array $values = null,
										$isBuffered   = true,
										$filter       = null)
	{
		if (! $this->isConnected()) {
			$this->setError(11000, 'must connect before query is issued');
			return false;
		}

		$status = false;
		$data   = null;
		$stmt   = $this->createPreparedStmt();
		if (! $stmt->prepare($sql)) {
			return new DbResponse($status, $data, $stmt->getError());
		}

		/* normalize and bind parameters */
		if (is_array($values) && ! empty($values)) {
			if (! $stmt->organizeParams($values)) {
				return new DbResponse($status, $data, $stmt->getError());
			}
		}

		if (! $stmt->execute()) {
			return new DbResponse($status, $data, $stmt->getError());
		}

		if (! $this->organizeResults()) {
			return new DbResponse($status, $data, $stmt->getError());
		}

	} 

	/**
	 * Excute a query represented by the sql.
	 *
	 * @param	string	$sql
	 * @param	string	$type 
	 * @param	bool	$isBuffered
	 * @param	mixed	$filter
	 * @return	DbResponse
	 */
	public function executeQuery($sql, 
								 $resultType = 'name', 
								 $isBuffered = true, 
								 $filter     = null)
	{
		if (! $this->isConnected()) {
			$this->setError(11000, 'must connect before query is issued');
			return false;
		}
		$query = $this->createQuery();

		$isBuffered =(bool) $isBuffered;
		$resultMode = MYSQLI_STORE_RESULT;
		if (! $isBuffered) {
			$resultMode = MYSQLI_USE_RESULT;
		}

		switch ($resultType) {
			/* column names as keys in the result */
			case 'name' :
				$resultType = MYSQLI_ASSOC;
				break;
			/* column position as keys in the result */
			case 'position':
				$resultType = MYSQLI_NUM;
				break;
			case 'both':
				$resultType = MYSQLI_BOTH;
				break;
			default:
				$resultType = MYSQLI_ASSOC;	
		}

		$result = $query->sendQuery($sql, $resultMode);
		
		/* query with no expected results */
		$data   = null;
		$error  = null;
		$status = false;
		if (true === $result) {
			$status = true;
		}
		/* failed query with errors */
		else if (false === $result || $query->isError()) {
			$error  = $query->getError();
		}
		/* valid result set */
		else if ($result instanceof Result) {
			$status = true;
			$data   = $result->fetchAllData($resultType, $filter);
			if ($data instanceof CallbackErrorInterface) {
				$error  = $data;
				$data   = null;
				$status = false;
			}
			$result->free();
		}

		return new DbResponse($status, $data, $error);
	}

	/**
	 * Close the establish connection with the database
	 * 
	 * @return bool
	 */
	public function close()
	{
		$server = $this->getServer();
		if (! $server->isConnected()) {
			return true;
		}

		if (! $server->close()) {
			$this->setError($server->getConnectionError());
			return false;
		}

		return true;
	}

    /**
     * @return  ErrorInterface
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
		return $this->error instanceof Error;
	}

	/**
	 * @return	Query
	 */
	protected function createQuery()
	{
		$server = $this->getServer();
		return new Query($server->getHandle());
	}

	/**
	 * @return	PreparedStmt
	 */
	protected function createPreparedStmt()
	{
		$server = $this->getServer();
		return new PreparedStmt($server->createStmtHandle());
	}


	/**
	 * The sub modules that this adapter oversees save their own errors and
	 * this allows us to assign them directly.
	 * 
	 * @param	Error	$error
	 * @return	null
	 */
	protected function assignError(Error $error)
	{
		$this->error = $error;
	}

	/**
	 * Used by the adapter to set an error based on the mysqi handle,
	 * 
	 * @param	int		$errNbr
	 * @param	string	$errText
	 * @param	string	$sqlState
	 * @return	null
	 */
	protected function setError($errNbr, $errText, $sqlState)
	{
		$this->assignError(new Error($errNbr, $errText, $sqlState));
	}
}
