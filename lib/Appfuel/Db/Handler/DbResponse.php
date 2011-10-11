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
namespace Appfuel\Db\Handler;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Handler\DbErrorInterface,
	Appfuel\Framework\Db\Handler\DbResponseInterface;

/**
 * Uniformally handles the dataset return back from the database. Has no 
 * knowledge of the database vendor.
 */
class DbResponse implements DbResponseInterface
{
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
	 * @var	DbErrorInterface
	 */
	protected $error = null;

	/**
	 * @param	array	$data
	 * @return	DbResponse
	 */
	public function __construct(array $data = null)
	{
		if (null !== $data) {
			$this->resultset = $data;
			$this->rowCount  = count($data);	
		}
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return $this->error instanceof DbErrorInterface;
	}

	/**
	 * @return	Error
	 */
	public function getError()
	{
		return $this->error;
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
