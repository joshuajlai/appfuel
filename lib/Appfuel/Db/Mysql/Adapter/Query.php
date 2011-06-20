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
	 * Performs a query against the database. All sql validation should
	 * be done prior to this call.
	 * 
	 * @return Select, show, decribe and explain return a result object
	 *		   other queries return true and false on failure
	 */
	public function send($sql, $resultMode = MYSQLI_STORE_RESULT)
	{
		$valid = array(MYSQLI_USE_RESULT,MYSQLI_STORE_RESULT);
		if (! in_array($resultMode, $valid)) {
			$err = "Invalid result mode given: must be constant" .
				   "MYSQLI_STORE_RESULT or MYSQLI_USE_RESULT";
			throw new Exception($err);
		}

		if (! is_string($sql) || empty($sql)) {
			throw new Exception("Invalid sql: must be a non empty string");
		}

		$handle = $this->getHandle();
	
		$result = $handle->query($sql, $resultMode);
		if ($result instanceof mysqli_result) {
			$result = $this->createResult($result);
		}

		return $result;
	}

	/**
	 * @param	mysqli_result	$handle
	 * @return	Result
	 */
	public function createResult(mysqli_result $handle)
	{
		return new Result($handle);
	}
}
