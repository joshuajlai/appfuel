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

use Closure,
	Exception,
	mysqli_stmt,
	mysqli_result,
	Appfuel\Error\ErrorStackInterface,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Wraps the mysqli_result. The reason we did not extend the mysqli_result is
 * to keep a consistent interface with the framework.
 */
class QueryResult implements MysqliResultInterface
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
	 * @var array
	 */
	protected $error = array();


	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli_result $handle)
	{
		$this->handle = $handle;
	}

	/**
	 * @param	mysqli_result	$handle	
	 * @return	QueryResult
	 */
	public function setHandle(mysqli_result $handle)
	{
		$this->handle = $handle;
		return $this;
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
	public function fetchAllData(ErrorStackInterface $errorStack,
								 $type = MYSQLI_ASSOC, 
								 $filter = null)
	{
		if (! $this->isHandle()) {
			$errorStack->addError("Result handle is not available", 500);
			return false;
		}

		if (! $this->isValidType($type)) {
			$this->free();
			$err = "fetchAllData failed: invalid result type -($type)";
			$errorStack->setError($msg, 500);
			return false;
		}
		
		/*
		 * No need to go foward knowing the callback is faulty
		 */
		if (! is_callable($filter) && ! empty($filter)) {
			$msg  = 'fetchAllData failed: invalid callback';
			$errorStack->seError($msg, 500);
			return false;
		}

		$idx    = 0;
		$handle = $this->getHandle();
		$data   = array();
		$error  = array();
		while ($row = $handle->fetch_array($type)) {
			if (! is_callable($filter)) {
				$data[] = $row;
				$idx++;
				continue;
			}

			try {
				$data[] = call_user_func($filter, $row);
			}
			catch (Exception $e) {
				$errText = $e->getMessage() . " -($idx)";
				$errorStack->addError($errText, $e->getCode());
				$data[] = false;
			}

			$idx++;
		}
		$this->free();

		return $data;
	}

	/**
	 * Grabs just the column names frim the getFields call
	 * 
	 * @return array
	 */
	public function getColumnNames()
	{
		if (! $this->isHandle()) {
			$this->setError(500, "Result handle has already been freed");
			return false;
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

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return ! empty($this->error);
	}

	/**
	 * @return	array
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	int	$code
	 * @param	string $msg
	 * @param	string $sqlState
	 * @return	QueryResult
	 */
	protected function setError($code, $msg, $sqlState = null)
	{
		$this->error = array(
			'error-nbr'  => $code,
			'error-text' => $msg,
			'sql-state'  => $sqlState
		);

		return $this;
	}

	/**
	 * @param	DictionaryInterface		$error
	 * @return	QueryResult
	 */
	protected function setErrorDictionary(DictionaryInterface $error)
	{
		$this->error = $error->getAll();
		return $this;
	}
}
