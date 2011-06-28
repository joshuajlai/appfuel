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
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(Mysqli $handle)
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
	 * Produces the resultset as a buffered set of data allowing you to 
	 * navigate the results or determine the number of rows returned etc..
	 * This comes at a cost in memory but easier to work with
	 * 
	 * @return Select, show, decribe and explain return a result object
	 *		   other queries return true and false on failure
	 */
	public function bufferedQuery($sql)
	{
		return $this->doQuery($sql, MYSQLI_STORE_RESULT);;
	}

	/**
	 * Produces an unbuffered resultset retrieving data as needed from the
	 * server. This has better performance on larger datasets, however no
	 * other queries can be run until the resultset is freed
	 *
	 * @return Select, show, decribe and explain return a result object
	 *		   other queries return true and false on failure
	 */
	public function unBufferedQuery($sql)
	{
		return $this->doQuery($sql, MYSQLI_USE_RESULT);
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
				echo "\n", print_r($handle->error,1), "\n";exit;
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
	 * @return Select, show, decribe and explain return a result object
	 *		   other queries return true and false on failure
	 */
	protected function doQuery($sql, $resultMode) 
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
		$result = $handle->query($sql, MYSQLI_USE_RESULT);
		if ($result instanceof mysqli_result) {
			$result = $this->createResult($result);
		}

		return $result;
	}
}
