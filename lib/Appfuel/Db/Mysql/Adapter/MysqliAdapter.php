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
	 * Mysqli handle used create prepared stmt and queries
	 * @var mysqli
	 */
	protected $handle = null;

	/**
	 * Error value object containing the last know error
	 * @var ErrorInterface
	 */
	protected $error = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli $handle)
	{
		$this->handle = $conn;
	}

	/**
	 * @return	Server
	 */
	public function getHandle()
	{
		return $this->handle;
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
										$filter       = null)
	{
		$stmt = $this->createPreparedStmt();
		if (! $stmt->prepare($sql)) {
			return new DbResponse(false, null, $stmt->getError());
		}

		/* normalize and bind parameters */
		if (is_array($values) && ! empty($values)) {
			if (! $stmt->organizeParams($values)) {
				return new DbResponse(false, null, $stmt->getError());
			}
		}

		if (! $stmt->execute()) {
			return new DbResponse($false, null, $stmt->getError());
		}
		
		$isOrganized = $stmt->organizeResults();
		if (! $stmt->organizeResults()) {
			return new DbResponse(false, null, $stmt->getError());
		}

		/* database executed the query successfully and 
		 * no results are needed
		 */
		if ($isOrganized && ! $stmt->isResultset()) {
			return new DbResponse(true);
		}
		
		$stmt->storeResults();

		$data = $stmt->fetch($filter);
		if ($data instanceof ErrorInterface) {
			return new DbResponse(false, null, $data);
		}
	
		$stmt->freeStoredResults();	
		return new DbResponse(true, $data);
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
	public function createQuery()
	{
		$server = $this->getServer();
		return new Query($this->getHandle());
	}

	/**
	 * @return	PreparedStmt
	 */
	public function createPreparedStmt()
	{
		$handle = $this->getHandle();
		return new PreparedStmt($handle->stmt_init());
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
	protected function setError($errNbr, $errText, $sqlState = null)
	{
		$this->assignError(new Error($errNbr, $errText, $sqlState));
	}
}
