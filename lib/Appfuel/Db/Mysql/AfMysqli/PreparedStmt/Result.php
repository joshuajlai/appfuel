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
namespace Appfuel\Db\Mysql\AfMysqli\PreparedStmt;

use Closure,
	mysqli_stmt,
	mysqli_result,
	Exception	as RootException,
	Appfuel\Db\DbError,
	Appfuel\Db\Mysql\AfMysqli\Result  as ParentResult,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\CallbackErrorInterface;

/**
 * Wraps the mysqli_result. The reason we did not extend the mysqli_result is
 * to keep a consistent interface with the framework.
 */
class Result extends ParentResult
{
    /**
     * Used to hold the results of the the prepared statement
     * @var array
     */
	protected $preparedData = array(
		'names'  => array(),
		'values' => array()
	);

	/**
	 * 
	 * @return bool
	 */
	public function organizePreparedResults(mysqli_stmt $stmtHandle)
	{
        /*
         * Preload column values with nulls
         */
        $cnames = $this->getColumnNames();
		
		if (! $cnames || $cnames instanceof DbError) {
			return $cnames;
		}


        $this->columnData = array(
            'names'  => $cnames,  
            'values' => array_fill(0, count($cnames), null)
        );

        $refs = array();
        foreach ($this->columnData['values'] as $index => &$var) {
            $refs[$index] = &$var;
        }

        /*
         * Bind to the result variables. We need the actual mysqli_stmt object
         */
        $ok = call_user_func_array(
            array($stmtHandle, 'bind_result'),
            $this->columnData['values']
        );

		return true;
	}

	/**
	 * Fetch resultset from a prepared statement
	 * 
	 * @param	mysli_stmt	$stmtHandle 
	 * @param	$filter		$null	callback or closure to filter a row
	 * @return	mixed
	 */
	public function fetchPreparedData(mysqli_stmt $stmtHandle, $filter = null)
	{
		$data   = array();
		$idx    = 0;
		$isNext = false;
		do {

			switch ($stmtHandle->fetch()) {
				case true:
					$row = array_combine(
						$this->columnData['names'],
						$this->dereferenceColumnValues()
					);

					$response = $this->filterResult($row, $filter);

					if ($response instanceof CallbackErrorInterface) {
						$response->setRowNumber($idx);
						$response->setRow($row);
						return $response;
					}

					$data[] = $response;
					$idx++;
					$isNext = true;	
				 break;

				case null:
					$isNext = false;	
					break;

				case false:
					return $this->createError(
						$stmtHandle->errno,
						$stmtHandle->error,
						$stmtHandle->sqlstate
					);
					
				default:
					$code = 'AF_PREPARED_RESULT';
					$msg  = 'unknown return value mysqli_stmt::fetch';
					return $this->createError($code, $msg);
			}

		} while ($isNext);

		$this->free();
		return $data;
	}


	/**
	 * Dereference the result values, otherwise things like fetchAll()
     * return the same values for every entry (because of the reference).
	 *
	 * @return	array
     */
	protected function dereferenceColumnValues()
	{
		if (! is_array($this->columnData['values'])) {
			throw new Exception("Column values need to be in an array");
		}

		$refs   = $this->columnData['values'];
		$values = array(); 
		foreach ($refs as $idx => $value) {
			$values[] = $value;
		}

		return $values;
	}
}
