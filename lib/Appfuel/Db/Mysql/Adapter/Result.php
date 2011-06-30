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

use Closure,
	mysqli_result,
	Exception as RootException,
	Appfuel\Framework\Exception,
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
	 * @return	int
	 */
	public function getRowCount()
	{
		$this->validateHandle('getNumRows');
		return $this->getHandle()
					->num_rows;
	}

	/**
	 * Adusts the result pointer to an arbitary row in the result
	 * 
	 * @param	int		$rowNbr
	 * @return  bool
	 */
	public function seekRow($rowNbr)
	{
		$this->validateHandle('seekRow');
		if (! is_numeric($rowNbr) || $rowNbr < 0) {
			return false;
		}

		return $this->getHandle()
					->data_seek($rowNbr);
	}

	/**
	 * Get a result row as an enumerated array. Fetches one row of data from 
	 * the result set and returns it as an enumerated array, where each column
	 * is stored in an array offset starting from 0. Each subsequent call to
	 * this will return the next row within the result set or null if ther is
	 * no more row. It would be more efficient to use getAllRows to avoid
	 * the repeated validation and get calls to the handle.
	 *
	 * @return	array | null
	 */
	public function fetchRow()
	{
		$this->validateHandle('fetchRow');
		return $this->getHandle()
					->fetch_row();
	}

	/**
	 * Process all rows and return the array
	 * 
	 * @return	array
	 */
	public function fetchAllRows()
	{
		$this->validateHandle('fetchAllRows');
		
		$handle  = $this->getHandle();
		$results = array();
		while ($row = $handle->fetch_row()) {
			$results[] = $row;
		}

		return $results;
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
		$this->validateHandle('fetchAllData');
		if (! $this->isValidType($type)) {
			$this->free();
			throw new Exception("invalid result type given");
		}

		/* no need to go foward knowing the callback is specified and
		 * has errors
		 */
		if (! is_callable($filter) && ! empty($filter)) {
			return new CallbackError('AF_RESULT_CALLBACK', 'unknown callback');
		}

		$handle = $this->getHandle();
		$data = array();
		$idx = 0;
		while ($row = $handle->fetch_array($type)) {

			$response = $this->resultFilter($row, $filter);

			if ($response instanceof CallbackErrorInterface) {
				$response->setRowNumber($idx);
				$response->setRow($row);
				return $response;
			}

			$data[] = $response;
			$idx++;
		}

		return array(
			'row-count' => $idx,
			'resultset' => $data	
		);
	}

	/**
	 * @param	mysqli_result	$handle
	 * @param	mixed			$filter
	 * @return	array
	 */
	protected function resultFilter(array $row, $filter = null)
	{
		if (empty($filter)) {
			return $row;
		}

		if ($filter instanceof Closure) {
			try {
			  $result = $filter($row);
			} catch (RootException $e) {
				$result = CallbackError($e->getCode(),$e->getMessage());
				$result->setCallbackType('closure');
			}

			return $result;
		}
		 
		if (is_callable($filter) && is_array($filter)) {
			try {
			  $result = call_user_func($filter, $row);
			} catch (RootException $e) {
				$result = CallbackError($e->getCode(),$e->getMessage());
				$result->setCallbackType('callback');
			}

			return $result;
		}
		
		if (is_callable($filter) && is_string($filter)) {
			try {
			  $result = $filter($row);
			} catch (RootException $e) {
				$result = CallbackError($e->getCode(),$e->getMessage());
				$result->setCallbackType('callback');
			}

			return $result;
		} 	

		return $row;
	}



	/**
	 * @param	int		$type
	 * @return	array
	 */
	public function fetchAll($type = MYSQLI_ASSOC)
	{
		$this->validateHandle('fetchAll');
		if (! $this->isValidType($type)) {
			$this->free();
			throw new Exception("invalid result type given");
		}

		return $this->getHandle()
					->fetch_all($type);
	}

	/**
	 * @param	int		$resultType 
	 * @return	array
	 */
	public function fetchArray($type = MYSQLI_ASSOC)
	{
		$this->validateHandle('fetchArray');
		if (! $this->isValidType($type)) {
			$this->free();
			throw new Exception("invalid result type given");
		}

		return $this->getHandle()
					->fetch_array($type);
	}

	/**
	 * Fetch a row as an associative array
	 * 
	 * @return	array
	 */
	public function fetchAssociativeArray()
	{
		$this->validateHandle('fetchAssociativeArray');
		return $this->getHandle()
					->fetch_assoc();
	}

	/**
	 * Fetch a row as an associative array
	 * 
	 * @param	string	$class	call to instantiate, set properties and return
	 * @param	array	$params	optional parameters to pass to constructor
	 * @return	array
	 */
	public function fetchObject($class = null, array $params = array())
	{
		$this->validateHandle('fetchObject');
		return $this->getHandle()
					->fetch_object();
	}


	/**
	 * @return int
	 */
	public function getFieldCount()
	{
		$this->validateHandle('getFieldCount');
		return $this->getHandle()
					->field_count;
	}

	/**
	 * Get the current field offset of the result pointer
	 *
	 * @return int
	 */
	public function getCurrentFieldNumber()
	{
		$this->validateHandle('getCurrentField');
		return $this->getHandle()
					->current_field;
	}

	/**
	 * Fetch meta data for a single field. We don't validate the handle 
	 * because we use getFieldCount which we know to validate.
	 * 
	 * @return	array
	 */
	public function getFieldMetadata($fieldNbr)
	{
		if (! is_numeric($fieldNbr) || $fieldNbr < 0) {
			return false;
		}

		$fieldCount = $this->getFieldCount();
		if ($fieldNbr >= $fieldCount) {
			return false;
		}
		
		return $this->getHandle()
					->fetch_field_direct($fieldNbr);
	}

	/**
	 * Returns the next field in the result set
	 *
	 * name			The name of the column
	 * orgname		Original column name if an alias was specified
	 * table		Name of the table this field belongs to (if not calculated)
	 * orgtable		Original table name if an alias was specified
	 * max_length	Maximum width of the field for the result set.
	 * length		Width of the field, as specified in the table definition.
	 * charsetnr	The character set number for the field.
	 * flags		An integer representing the bit-flags for the field.
	 * type			The data type used for this field
	 * decimals		The number of decimals used (for integer fields)
	 * 
	 * @return	StdClass | false
	 */
	public function getField()
	{
		$this->validateHandle('getField');

		return $this->getHandle()
					->fetch_field();
	}

	/**
	 * @return	array | false
	 */
	public function getFields()
	{
		$this->validateHandle('getFields');

		return $this->getHandle()
					->fetch_fields();
	}

	/**
	 * Grabs just the column names frim the getFields call
	 * 
	 * @return array
	 */
	public function getColumnNames()
	{
		$fields = $this->getFields();
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
	 * Adusts the result pointer to an arbitary row in the result
	 * 
	 * @param	int		$rowNbr
	 * @return  bool
	 */
	public function fieldSeek($fieldNbr)
	{
		$this->validateHandle('fieldSeek');
		if (! is_numeric($fieldNbr) || $fieldNbr < 0) {
			return false;
		}

		return $this->getHandle()
					->field_seek($fieldNbr);
	}

	/**
	 * Returns an array of integers representing the size of each column
	 * not including the terminating null characters. 
	 *
	 * @return array | false on error
	 */
	public function getColumnLengths()
	{
		$this->validateHandle('getColumnLengths');
		return $this->getHandle()
					->lengths;
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
	 * Ensures that the handle is available and ready to use
	 *
	 * @return null
	 */
	protected function validateHandle($method)
	{
		if (! $this->isHandle()) {
			$err = 'Result handle has been freed:';
			throw new Exception("$err operation ($method) is invalid");
		}
	}
}
