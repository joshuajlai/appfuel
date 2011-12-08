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
namespace Appfuel\Db\Mysql\Mysqli\MultiQuery;

use mysqli as MysqliDriver,
	mysqli_result,
	Appfuel\Db\DbResponse,
	Appfuel\Db\DbError,
	Appfuel\Db\Mysql\Mysqli\Result,
	Appfuel\Framework\Db\DbErrorInterface;

/**
 * Handles all low level routines that deal with issuing a query
 */
class Stmt
{
	/**
	 * Executes a series of queries joined togather by a ';'. Once executed
	 * we look into the options array for each dataset using its index
	 * (basically the number it appeared in the concatenated string) looking
	 * for a key to replace the index with and a callback to use on each row
	 * of the dataset
	 *
	 * @param	mysqli	$drv		mysqli driver
	 * @param	string	$sql		sql statements joined into one string
	 * @param	array	$options	map used to convert dataset indexes into
	 *								meaningful keys and hold callbacks
	 * @return array	list of DbResponseInterface
	 */
	public function execute(mysqliDriver $drv, 
								   $sql, 
							array  $createResponse,
							array  $options = array())
	{

		if (! is_callable($createResponse)) {
			$errCode = 'AF_ERR_BAD_CALLBACK';
			$errTxt  = 'reponse callback is not valid';
			$error = new Error(-1, $errCode, $errTxt);
			return call_user_func($createResponse);
		}

		/* 
		 * -1 key indicated the loop never ran and this most likely a 
		 * syntax error. 
		 */
		if (! $drv->multi_query($sql)) {
			$error = new Error(-1, $drv->errno, $drv->error, $drv->sqlstate);
			return call_user_func($createResponse, $error);
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

			$resultKey = $idx;
			if ($isResultKey) {
				$resultKey = $options[$idx]['result-key'];
			}

			$callback = null;
			if ($isCallback) {
				$callback = $options[$idx]['callback'];	
			}

			/*
			 * Returns a buffered result object or false if an error occured
			 */
			$driverResult = $drv->store_result();
			if (! $driverResult) {
				$error = new Error(
					$resultKey,
					$drv->errno, 
					$drv->error, 
					$drv->sqlstate
				);

				$data[$resultKey] = call_user_func($createResponse, $error);
				return $data;
			}

			$result  = new Result($driverResult);
			$fetched = $result->fetchAllData(MYSQLI_ASSOC, $callback);
			$data[$resultKey]  = call_user_func($createResponse, $fetched);
			
			$isMore = $drv->more_results();
			if ($isMore) {
				$drv->next_result();
				$idx++;
			}
		} while ($isMore);
	
		return $data;
	}
}
