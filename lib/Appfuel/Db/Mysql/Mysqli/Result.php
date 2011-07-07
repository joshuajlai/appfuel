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

use Closure,
	mysqli_stmt,
	mysqli_result,
	Exception as RootException,
	Appfuel\Framework\Exception,
	Appfuel\Db\DbError,
	Appfuel\Db\Mysql\CallbackError,
	Appfuel\Framework\Db\Adapter\CallbackErrorInterface;

/**
 * Wraps the mysqli_result. The reason we did not extend the mysqli_result is
 * to keep a consistent interface with the framework.
 */
class Result
{
	/**
	 * Mysqli object used to interact with the database
	 * @var	Mysqli
	 */	
	protected $handle = null;

	/**
	 * This optional parameter is a constant indicating what type of array 
	 * should be produced from the current row data.
	 * @var array
	 */
	protected $validTypes = array(
		MYSQLI_ASSOC,
		MYSQLI_NUM,
		MYSQLI_BOTH
	);

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli_result $handle)
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
	 * @return bool
	 */
	public function isHandle()
	{
		return $this->handle instanceof mysqli_result;
	}

	/**
	 * @param	int		$type
	 * @return	bool
	 */
	public function isValidType($type)
	{
		if (in_array($type, $this->validTypes)) {
			return true;
		}
	
		return false;
	}

	/**
	 * Fetch all the rows allowing access to each row with a callback or 
	 * closure
	 *
	 * @param	int		$type
	 * @param	mixed	string | array | closure
	 * @return	array
	 */
	public function fetchAllData($type = MYSQLI_ASSOC, $filter = null)
	{
		if (! $this->isHandle()) {
			$code = 'AF_RESULT_NO_HANDLE';
			$msg = 'Result handle has been freed:';
			return $this->createError($code, $msg);
		}

		if (! $this->isValidType($type)) {
			$this->free();
			$code = 'AF_RESULT_BAD_TYPE';
			$msg  = "invalid result type passed in as $type";
			return $this->createError($code, $msg);
		}

		/* no need to go foward knowing the callback is specified and
		 * has errors
		 */
		if (! is_callable($filter) && ! empty($filter)) {
			$code = 'AF_RESULT_CALLBACK';
			$msg  = 'unknown callback';
			return $this->createCallbackError($code, $msg);
		}

		$handle = $this->getHandle();
		$data = array();
		$idx = 0;
		while ($row = $handle->fetch_array($type)) {

			$response = $this->filterResult($row, $filter);

			if ($response instanceof CallbackErrorInterface) {
				$response->setRowNumber($idx);
				$response->setRow($row);
				return $response;
			}

			$data[] = $response;
			$idx++;
		}
		$this->free();

		return $data;
	}

	/**
	 * @param	mysqli_result	$handle
	 * @param	mixed			$filter
	 * @return	array
	 */
	protected function filterResult(array $row, $filter = null)
	{
		if (empty($filter)) {
			return $row;
		}

		if ($filter instanceof Closure) {
			try {
			  $result = $filter($row);
			} catch (RootException $e) {
				$code = $e->getCode();
				$msg  = $e->getMessage();
				$result = $this->createCallbackError($code, $msg);
				$result->setCallbackType('closure');

			}

			return $result;
		}
		 
		if (is_callable($filter) && is_array($filter)) {
			try {
			  $result = call_user_func($filter, $row);
			} catch (RootException $e) {
				$code = $e->getCode();
				$msg  = $e->getMessage();
				$result = $this->createCallbackError($code, $msg);
				$result->setCallbackType('closure');
			}

			return $result;
		}
		
		if (is_callable($filter) && is_string($filter)) {
			try {
			  $result = $filter($row);
			} catch (RootException $e) {
				$code = $e->getCode();
				$msg  = $e->getMessage();
				$result = $this->createCallbackError($code, $msg);
				$result->setCallbackType('closure');
			}

			return $result;
		} 	

		return $row;
	}

	/**
	 * Grabs just the column names frim the getFields call
	 * 
	 * @return array
	 */
	public function getColumnNames()
	{
		if (! $this->isHandle()) {
			$code = 'AF_RESULT_NO_HANDLE';
			$msg = 'Result handle has been freed:';
			return $this->createError($code, $msg);
		}

		$fields = $this->getHandle()
					   ->fetch_fields();
		if (! is_array($fields)) {
			return false;
		}

		$names = array();
		foreach ($fields as $field) {
			$names[] = $field->name;
		}

		return $names;
	}

	/**
	 * Free the resultset from memory and remove its reference
	 *
	 * @return null
	 */
	public function free()
	{
		if (! $this->isHandle()) {
			return;
		}

		$this->handle->free();
		$this->handle = null;
	}

	protected function createError($code, $msg, $sqlState = null)
	{
		return new DbError($code, $msg, $sqlState);
	}

	protected function createCallbackError($code, $msg)
	{
		return new CallbackError($code, $msg);
	}
}
