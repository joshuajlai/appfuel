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
							DbRequestInterface $request,
							DbResponseInterface $response)
	{
        $sql     = $request->getSql();
        $options = $request->getResultOptions();

        /* 
         * -1 key indicated the loop never ran and this most likely a 
         * syntax error. 
         */
        if (! $driver->multi_query($sql)) {
            $error = new Error(-1, $drv->errno, $drv->error, $drv->sqlstate);
            return $this->createResponse($error);
        }

        /* index for each query, this is mapped to the result keys */
        $idx  = 0;
        $data = array();

        do {
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

            /*
             * Returns a buffered result object or false if an error occured
             */
            $driverResult = $driver->store_result();
            if (! $driverResult) {
                $error = new Error(
                    $resultKey,
                    $drv->errno,
                    $drv->error,
                    $drv->sqlstate
                );

                $data[$resultKey] = $this->createResponse($error);
                return $data;
            }

            $result  = new Result($driverResult);
            $fetched = $result->fetchAllData(MYSQLI_ASSOC, $callback);
            $data[$resultKey]  = $this->createResponse($fetched);

            $isMore = $drv->more_results();
            if ($isMore) {
                $drv->next_result();
                $idx++;
            }
        } while ($isMore);

        return $this->createResponse($data);
	}
}
