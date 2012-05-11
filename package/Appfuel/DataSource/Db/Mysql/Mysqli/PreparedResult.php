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
	mysqli_stmt,
	mysqli_result,
	Exception,
	RunTimeException,
	Appfuel\Error\ErrorStackInterface,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Wraps the mysqli_result. The reason we did not extend the mysqli_result is
 * to keep a consistent interface with the framework.
 */
class PreparedResult 
	extends QueryResult implements MysqliPreparedResultInterface
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
	public function organizePreparedResults(mysqli_stmt $stmt)
	{
        /*
         * Preload column values with nulls
         */
        $cnames = $this->getColumnNames();
		if (! $cnames || $this->isError()) {
			return false;
		}

        $this->columnData = array(
            'names'  => $cnames,  
            'values' => array_fill(0, count($cnames), null)
        );

		/*
		 * bind_result expects its arguments to be passed by reference
		 */
        $refs = array();
        foreach ($this->columnData['values'] as $index => &$var) {
            $refs[$index] = &$var;
        }

        /*
         * Bind to the result variables. We need the actual mysqli_stmt object
         */
        $ok = call_user_func_array(
            array($stmt, 'bind_result'),
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
	public function fetchPreparedData(mysqli_stmt $stmt,
									  ErrorStackInterface $errorStack, 
									  $filter = null)
	{
		$data   = array();
		$idx    = 0;
		$isNext = false;
		do {

			switch ($stmt->fetch()) {
				case true:
					$row = array_combine(
						$this->columnData['names'],
						$this->dereferenceColumnValues()
					);
					
					if (is_callable($filter)) {
						try {
							$row = call_user_func($filter, $row);
						} 
						catch (Exception $e) {	
							$errText = $e->getMessage() . " -($idx)";
							$errorStack->addError($errText, $e->getCode());
							$row = false;
						}
					}
					$data[] = $row;
					$idx++;
					$isNext = true;	
				 break;

				case null:
					$isNext = false;	
					break;

				case false:
					$error = $stmt->error . " -($idx) " . $stmt->sqlstate;
					$errorStack->addError($error, $stmt->errorno);
					return false;
	
				default:
					$msg  = "unknown return value mysqli_stmt::fetch -($idx)";
					$errorStack->addError(500, $msg);
					return false;
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
			throw new RunTimeException("Column values need to be in an array");
		}

		$refs   = $this->columnData['values'];
		$values = array(); 
		foreach ($refs as $idx => $value) {
			$values[] = $value;
		}

		return $values;
	}
}
