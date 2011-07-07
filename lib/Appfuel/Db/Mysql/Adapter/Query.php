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

use mysqli,
	mysqli_result,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Mysql\Adapter\QueryInterface;

/**
 * Handles all low level routines that deal with issuing a query
 */
class Query implements QueryInterface
{
	/**
	 * Mysqli object used to interact with the database
	 * @var	Mysqli
	 */	
	protected $handle = null;

	/**
	 * Holds error information for query
	 * @var Error
	 */
	protected $error = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli $handle)
	{
		$this->handle = $handle;
	}

	/**
	 * @return	Mysqli
	 */
	public function getHandle()
	{
		return $this->handle;
	}

	/**
	 * @return	Error
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
	 * Produces a result object as either buffered or unbuffered depending
	 * on the result mode.
	 * Buffered set of data allows you to navigate the results or determine 
	 * the number of rows returned etc.. This comes at a cost in memory but 
	 * easier to work with.
	 * 
	 * Unbuffered resultset retrieving data as needed from the
	 * server. This has better performance on larger datasets, however no
	 * other queries can be run until the resultset is freed
	 *
	 *
	 * @return Select, show, decribe and explain return a result object
	 *		   other queries return true and false on failure
	 */
	public function sendQuery($sql, $resultMode = MYSQLI_STORE_RESULT) 
	{		

		if (! is_string($sql) || empty($sql)) {
			throw new Exception("Invalid sql: must be a non empty string");
		}

		$valid = array(MYSQLI_USE_RESULT,MYSQLI_STORE_RESULT);
		if (! in_array($resultMode, $valid)) {
			$err = "Invalid result mode given: must be constant" .
				   "MYSQLI_STORE_RESULT or MYSQLI_USE_RESULT";
			throw new Exception($err);
		}

		$handle = $this->getHandle();
		$result = $handle->query($sql, $resultMode);
		if (! $result) {
			$this->setError($handle->errno, $handle->error, $handle->sqlstate);
			return false;
		}
		else if ($result instanceof mysqli_result) {
			return $this->createResult($result);
		}
		else {
			return $result;
		}
	}

	/**
	 * Allows more than one query tobe excuted
	 *
	 * @return 
	 */
	public function multipleQuery($sql)
	{
		if (! is_string($sql) || empty($sql)) {
			throw new Exception("Invalid sql: must be a non empty string");
		}

		$handle = $this->getHandle();
		if (! $handle->multi_query($sql)) {
			$this->setError($handle->errno, $handle->error, $handle->sqlstate);
			return false;
		}
	
		$qIndex = 0;
		$data = array();
		do {
			$data[$qIndex] = array();
			if ($result = $handle->store_result()) {
				$data[$qIndex] = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
			}
			$isMore = $handle->more_results();
			if ($isMore) {
				$handle->next_result();
				$qIndex++;
			}
		} while ($isMore);
	
		return $data;
	}

	/**
	 * @param	mysqli_result	$handle
	 * @return	Result
	 */
	public function createResult(mysqli_result $handle)
	{
		return new Result($handle);
	}

	/**
	 * @param	int		$errNbr
	 * @param	string	$errText
	 * @param	string	$sqlState
	 * @return	null
	 */
	protected function setError($errNbr, $errText, $sqlState = null)
	{
		$this->error = new Error($errNbr, $errText, $sqlState);
	}
}
