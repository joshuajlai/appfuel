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

use RunTimeException,
	mysqli_result,
	mysqli as MysqliDriver,
	Appfuel\DataSource\Db\DbResponseInterface,
	Appfuel\DataSource\Db\DbRequestInterface;

/**
 * This is the default request type which sends a regular database query to 
 * the database server packages errors or result data (apply callbacks) into
 * a DbResponse.
 */
class QueryAdapter implements MysqliAdapterInterface
{
	/**
	 * @param	MysqliDriver (mysqli) $driver
	 * @param	DbRequestInterface $request
	 * @param	DbResponseInterface $response
	 * @return	DbResponseInterface
	 */
	public function execute(MysqliDriver        $driver,
							DbRequestInterface  $request,
							DbResponseInterface $response)
	{
        $mode   = MYSQLI_STORE_RESULT;
        if (! $request->isResultBuffer()) {
            $mode = MYSQLI_USE_RESULT;
        }
        switch ($request->getResultType()) {
            /* column names as keys in the result */
            case 'name' :
                $type = MYSQLI_ASSOC;
                break;
            /* column position as keys in the result */
            case 'position':
                $type = MYSQLI_NUM;
                break;
            case 'name-pos':
                $type = MYSQLI_BOTH;
                break;
            default:
                $type = MYSQLI_ASSOC;
        }

        $sql    = $request->getSql();
        $filter = $request->getCallback();

        try {
            $resultHandle = $driver->query($sql, $mode);
        } catch (\Exception $e) {
			$response->addError($e->getMessage(), $e->getCode());
			return $response;
        }

        if (! ($resultHandle instanceof mysqli_result)) {
			$class = get_class($this);
			$response->addError("{$class} expected mysqli_result none given");

			$text = "{$driver->error} {$driver->sqlstate}";
			$response->addError($text, $driver->errno);
			return $response;
		}
            
		$result	= new QueryResult($resultHandle);
		$stack  = $response->getErrorStack();
        $data	= $result->fetchAllData($stack, $type, $filter); 
		if ($stack->isError()) {
			$response->markFailure();
		}

		/*
		 * setAffectedRows has the final way on whether the response is marked
		 * as failure
		 */
		$response->setResultSet($data);
		$response->setAffectedRows($driver->affected_rows);	
		return $response;
	}
}
