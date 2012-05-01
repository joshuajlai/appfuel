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
	mysqli as MysqliDriver,
	Appfuel\Error\ErrorItem,
	Appfuel\DataSource\Db\DbResponse,
	Appfuel\DataSource\Db\DbResponseInterface,
	Appfuel\DataSource\Db\DbRequestInterface;

/**
 * The database adapter is 
 */
class MultiQueryAdapter implements MysqliAdapterInterface
{
	/**
	 * @param	DbRequestInterface $request
	 * @return	DbReponseInterface
	 */
	public function execute(MysqliDriver $driver,
							DbRequestInterface  $request,
							DbResponseInterface $mainResponse)
	{
        $sql     = $request->getSql();
        $options = $request->getMultiResultOptions();

        /* 
         * -1 key indicated the loop never ran and this most likely a 
         * syntax error. 
         */
        if (! $driver->multi_query($sql)) {
			$error = $this->createErrorItem(-1, $driver);
            $response->addError($error);
            return $response;
        }

        /* index for each query, this is mapped to the result keys */
        $idx  = 0;
        $data = array();
		$totalAffected = 0;
        do {
			$resultResponse = new DbResponse();

            /*
             * check for the existence of all available options
             */
            $isOption     = isset($options[$idx]);
            $isResultKey  = $isOption && isset($options[$idx]['result-key']);
            $isCallback   = $isOption && isset($options[$idx]['callback']);

            $resultKey = $idx;
            if ($isResultKey) {
                $resultKey = $options[$idx]['result-key'];
            }

            $callback = null;
            if ($isCallback) {
                $callback = $options[$idx]['callback'];
            }

            $driverResult = $driver->store_result();
            if (! $driverResult) {
				$error = $this->createErrorItem($resultKey, $driver);
				/*
				 * Each query in a multi query has its own response but
				 * we also want the main response to know about each error
				 * so we give it a copy as well
				 */
				$resultResponse->addError($error);
				$mainResponse->addError($error);
                $data[$resultKey] = $resultResponse;
            }
			else {
				$result    = new QueryResult($driverResult);
				$stack     = $resultResponse->getErrorStack();
				$dbReturn  = $result->fetchAllData(
					$stack,
					MYSQLI_ASSOC, 
					$callback
				);

				/* 
				 * merge a copy of the error items into the main response
				 */
				if ($stack->isError()) {
					$mainResponse->markFailure()
								 ->getErrorStack()
								 ->mergeStack($stack);
				}

				$affected = $driver->affected_rows;	
				$resultResponse->setResultSet($dbReturn);
				$resultResponse->setAffectedRows($affected);

				/* cast so we can blindly add */
				$affected =(int) $affected;
				if ($affected > 0) {
					$totalAffected += $affected;
				}

				$data[$resultKey]  = $resultResponse;
			}

            $isMore = $driver->more_results();
            if ($isMore) {
                $driver->next_result();
                $idx++;
            }
        } while ($isMore);

		$mainResponse->setResultSet($data);
		$mainRespinse->setAffectedRows($totalAffected);
        return $mainResponse;
	}

	public function createErrorItem($key, MysqliDriver $driver)
	{
		$text = "{$key}:{$driver->error}:{$driver->sqlstate}";
		$code = $driver->errno;
		return new ErrorItem($text, $code);
	}
}
