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
	mysqli_result,
	Appfuel\Db\DbResponse,
	Appfuel\Db\DbError,
	Appfuel\Db\Mysql\Mysqli\Result,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\DbErrorInterface,
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
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli_handle $handle)
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
	public function execute($sql, 
							$mode = MYSQLI_STORE_RESULT,
							$type = MYSQLI_ASSOC, 
							$filter = null)
	{		

		if (! is_string($sql) || empty($sql)) {
			throw new Exception("Invalid sql: must be a non empty string");
		}

		$valid = array(MYSQLI_USE_RESULT,MYSQLI_STORE_RESULT);
		if (! in_array($mode, $valid)) {
			$err = "Invalid result mode given: must be constant" .
				   "MYSQLI_STORE_RESULT or MYSQLI_USE_RESULT";
			throw new Exception($err);
		}

		$handle = $this->getHandle();
		$result = $handle->query($sql, $mode);

		/*
		 * Handle errors, valid results and queries that ran but need no 
		 * results
		 */ 
		if (! $result) {
			$error = $this->createError(
				$handle->errno, 
				$handle->error, 
				$handle->sqlstate
			);

			return $this->createResponse($error);
		}
		else if ($result instanceof mysqli_result) {
			$result = $this->createResult($result);
			$data   = $result->fetchAllData($type, $filter);
			return $this->createResponse($data);
		}
		else {
			return $this->createResponse();
		}
	}

	/**
	 * Allows more than one query tobe excuted
	 *
	 * @return 
	 */
	public function executeMultiple($sql, array $options = array())
	{
		if (! is_string($sql) || empty($sql)) {
			throw new Exception("Invalid sql: must be a non empty string");
		}

		$handle = $this->getHandle();
		if (! $handle->multi_query($sql)) {
			/* -1 key indicated the loop never ran and this 
			 * is a syntax error 
			 */
			$error = $this->createMultiQueryError(
				-1,
				$handle->errno, 
				$handle->error, 
				$handle->sqlstate
			);

			return $this->createResponse($error);
		}
	
		/* index for each query, this is mapped to the result keys */
		$idx  = 0;
		$data = array();

		do {
			/*
			 * check for the existence of all available options
			 */
			$isOption     = isset($options[$idx]);
			$isResultKey  = $isOption && isset($options[$idx]['result-key']);
			$isCallback   = $isOption && isset($options[$idx]['callback']);

			/* check if we needed to map the result index to a user 
			 * defined key
			 */
			$resultKey = $idx;
			if ($isResultKey) {
				$resultKey = $options[$idx]['result-key'];
			}

			/*
			 * A callback can be specified for each query issued
			 */
			$callback = null;
			if ($isCallback) {
				$callback = $options[$idx]['callback'];	
			}

			/*
			 * Returns a buffered result object or false if an error occured
			 */
			$mysqliRS = $handle->store_result();
			if (! $mysqliRS) {
				$error = $this->createMultiQueryError(
					$resultKey,
					$handle->errno, 
					$handle->error, 
					$handle->sqlstate
				);

				$data[$resultKey] = $this->createResponse($error);
				return $data;
			}

			$result  = $this->createResult($mysqliRS);
			$fetched = $result->fetchAllData(MYSQLI_ASSOC, $callback);
			$data[$resultKey]  = $this->createResponse($fetched);
			
			
			$isMore = $handle->more_results();
			if ($isMore) {
				$handle->next_result();
				$idx++;
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
	 * @param	int		$nbr
	 * @param	string	$txt
	 * @param	string	$sqlState
	 * @return	null
	 */
	protected function createError($nbr, $txt, $sqlState = null)
	{
		return new DbError($nbr, $txt, $sqlState);
	}

	/**
	 * @param	int		$errNbr
	 * @param	string	$errText
	 * @param	string	$sqlState
	 * @return	null
	 */
	protected function createMultiQueryError($key, $nbr, $txt, $sqlState = null)
	{
		return new MultiQueryError($key, $nbr, $txt, $sqlState);
	}


	protected function createResponse($data = null)
	{
		$response = null;
		if (null === $data) {
			$response =  new DbResponse();
		}
		else if (is_array($data)) {
			$response = new DbResponse($data);
		}
		else if ($data instanceof DbErrorInterface) {
			$response = new DbResponse(null, $data);
		}
		
		return $response;
	}

}
