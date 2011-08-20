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
namespace Appfuel\Db\Mysql\AfMysqli\Query;

use mysqli,
	mysqli_result,
	Appfuel\Db\DbError,
	Appfuel\Db\Mysql\AfMysqli\Result;

/**
 * Handles all low level routines that deal with issuing a query
 */
class Stmt
{
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
	public function execute(mysqli $driver,
							$sql, 
							$mode = MYSQLI_STORE_RESULT,
							$type = MYSQLI_ASSOC, 
							$filter = null)
	{
		$errCode = 'AF_ERR_MYSQLI_QUERY_STMT';
		if (empty($sql)) {
			$err = 'Invalid execute: sql is empty';
			return new DbError($errCode, $err);
		}

		$valid = array(MYSQLI_USE_RESULT,MYSQLI_STORE_RESULT);
		if (! in_array($mode, $valid)) {
			$err = "Invalid execute: result mode is value";
			return new DbError($errCode, $err);
		}

		try {
			$result = $driver->query($sql, $mode);
		} catch (\Exception $e) {
			return new DbError($e->getCode(), $e->getMessage());
		}

		if ($result instanceof mysqli_result) {
			$afResult = new Result($result);
			$result = $afResult->fetchAllData($type, $filter);
		}

		return $result;
	}
}
