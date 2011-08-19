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
namespace Appfuel\Db;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\DbErrorInterface as ErrorInterface,
	Appfuel\Framework\Db\DbResponseInterface;

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
	protected $resultset = null;

	/**
	 * Number of rows returned for the query issued
	 * @var int
	 */
	protected $rowCount = 0;

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
	public function __construct(array $data = null, ErrorInterface $err = null)
	{
		$this->status = true;
		
		/* set error and toggle status to false */
		if ($err !== null) {
			$this->error  = $err;
			$this->status = false;
		}
	    
		/* we save the database recordset and check if it is structured
		 * with the correct keys 
		 */	
		if ($data !== null  && true === $this->status) {
			$this->resultset = $data;
			$this->rowCount  = count($data);	
		}
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
		return $this->error instanceof ErrorInterface;
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
	 * @return	int | false on failure
	 */
	public function getRowCount()
	{
		return $this->rowCount;
	}

	/**
	 * @return	array | false on fauilure
	 */
	public function getResultset()
	{
		return $this->resultset;
	}

	/**
	 * Returns the first item in the resultset
	 * 
	 * @return	mixed
	 */
	public function getCurrentResult()
	{
		if (is_array($this->resultset)) {
			return current($this->resultset);
		}

		return false;
	}
}
