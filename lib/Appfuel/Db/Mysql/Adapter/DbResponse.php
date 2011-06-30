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

use Appfuel\Framework\Db\Adapter\DbResponseInterface;

/**
 * Normalize the meaning of a database response by providing a status, error 
 * and actual data given by the database. When queries that do not produce 
 * result are used you can check the status determine if they were successful.
 */
class DbResponse implements DbResponseInterface
{
	/**
	 * Flag used to indicate the state of the returned data. True means
	 * the query ran as expected false means errors are present
	 * @var bool
	 */
	protected $status = null;

	/**
	 * Holds the information requested from the database
	 * @var array
	 */
	protected $data = null;

	/**
	 * Error object holding all error info about the query just issued
	 * @var	Error
	 */
	protected $error = null;

	/**
	 * @param	bool	$status 
	 * @param	array	$data
	 * @param	Error	$error
	 * @return	DbResponse
	 */
	public function __construct($status, array $data = null, Error $err = null)
	{
		$this->status =(bool) $status;
		$this->data   = $data;
		$this->error  = $err;
	}

	/**
	 * @return bool
	 */
	public function isSuccess()
	{
		return true === $this->status && empty($this->error);
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return $this->error instanceof Error;
	}

	/**
	 * @return	Error
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return	bool
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**	
	 * @return	array | null
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Used to determine that content returned from the database is in known
	 * structure
	 *
	 * @return	bool
	 */
	public function isValidDataStructure()
	{
		return is_array($this->data)			&& 
			   isset($this->data['row-count'])  &&
			   isset($this->data['resultset'])  &&
			   is_array($this->data['resultset']);
	}

	/**
	 * @return	int | false on failure
	 */
	public function getRowCount()
	{
		if (! $this->validDataStructure()) {
			return false;
		}

		return $this->data['row-count'];
	}

	/**
	 * @return	array | false on fauilure
	 */
	public function getRecordset()
	{
		if (! $this->validDataStructure()) {
			return false;
		}
		
		return $this->data['recordset'];
	}

	/**
	 * Returns the first item in the resultset
	 * 
	 * @return	mixed
	 */
	public function getCurrentDataItem()
	{
		if (! $this->validDataStructure()) {
			return false;
		}
		
		return current($this->data['recordset']);
	}
}
