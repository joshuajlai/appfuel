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
namespace Appfuel\DataSource\Db\Mysql\Mysqli;

use mysqli_result;

/**
 */
interface MysqliResultInterface
{
	/**
	 * @param	mysqli_result	$handle
	 * @return	MysqliResultInterface
	 */
	public function setHandle(mysqli_result $handle);

	/**
	 * @return	Mysqli
	 */
	public function getHandle();

	/**
	 * @return bool
	 */
	public function isHandle();

	/**
	 * Type determines how the resulting array is returned. Valid types 
	 * include:
	 * 
	 *	MYSQLI_ASSOC
	 *	MYSQLI_NUM
	 *  MYSQLI_BOTH
	 * 
	 * 1) will return true for all the above constants and false for 
	 *    everything else
	 *
	 * @param	int		$type
	 * @return	bool
	 */
	public function isValidType($type);

	/**
	 * Fetch all the rows allowing access to each row with a callback or 
	 *
	 * 1) When no handle exist, set error with code 500
	 * 2) When type is not valid, set set error with code 500
	 * 3) When filter (callback) is given but not callable then set error
	 *    code 500 
	 * 4) For every row filter the result set with a callback if given
	 * 5) Free resultset
	 * 
	 * @param	int		$type
	 * @param	mixed	string | array | closure
	 * @return	array
	 */
	public function fetchAllData($type = MYSQLI_ASSOC, $filter = null);

	/**
	 * 1) If no callback is given return the original row
	 * 2) Apply the filter callback return the filtered row
	 *	  Catch any exceptions and put them into a DictionaryInterface and
	 *	  return that dictionary
	 * 
	 * @param	mysqli_result	$handle
	 * @param	mixed			$filter
	 * @return	array
	 */
	public function filterResult(array $row, $index, $filter = null);

	/**
	 * Grabs just the column names frim the getFields call
	 * 
	 * @return array
	 */
	public function getColumnNames();

	/**
	 * Free the resultset from memory and remove its reference
	 *
	 * @return null
	 */
	public function free();

	/**
	 * @return	bool
	 */
	public function isError();

	/**
	 * @return	array
	 */
	public function getError();
}
