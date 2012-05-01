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
namespace Appfuel\Db\Mysql\Mysqli;

use RunTimeException,
	mysqli as MysqliDriver,
	Appfuel\Db\DbResponse,
	Appfuel\Db\DbRequestInterface,
	Appfuel\Kernel\FrameworkObject;

/**
 * The database adapter is 
 */
class DbAdapter implements DbAdapterInterface
{
	/**
	 * @var	mysqli
	 */
	protected $driver = null;

	/**
	 * @param	mysqli $driver
	 * @return	AdapterBase
	 */
	public function __construct(MysqliDriver $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return	mysqli
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @param	DbRequestInterface $request
	 * @return	DbReponseInterface
	 */
	public function execute(DbRequestInterface $request,
							DbResponseInterface $response)
	{
		$type     = $request->getType();
		switch($type) {
			case 'query': 
				$this->query($request, $response);		 
				break;
			case 'multi-query':	  
				$this->multiQuery($request, $response);    
				break;
			case 'prepared-stmt': 
				$this->preparedQuery($request, $response); 
				break;
			default: throw new RunTimeException(
				"Invalid request type mysqli query adapter can not execute"
			);
		}

		return $response;
	}

	/**
	 * @param	DbRequestInterface $request
	 * @return	DbResponseInterface
	 */
	public function query(DbRequestInterface $request)
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

        $driver = $this->getDriver();
        try {
            $result = $driver->query($sql, $mode);
        } catch (\Exception $e) {
			$this->addError($e->getMessage(), $e->getCode());
			return false;
        }

        if (! ($result instanceof mysqli_result)) {
			$this->addError("result not instanceof mysqli_result");
			return false;
		}
            
		$resultSet = new ResultSet($result);
        $data = $resultSet->fetchAllData($type, $filter);
        
        if (false === $data) {
			$errText = "{$driver->error} {$drive->sqlstate}";
			$errCode = $driver->errno;
			$this->addError($errText, $errCode);
			return false;
        } else if (true === $data) {
            $data = null;
        }

        return $this->createResponse($data);
	}

	/**
	 * @param	DbRequestInterface $request
	 * @return	DbResponseInterface
	 */
	public function multiQuery(DbRequestInterface $request)
	{
        $driver  = $this->getDriver();
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

	/**
	 * @param	DbRequestInterface $request
	 * @return	DbResponseInterface
	 */
	public function preparedQuery(DbRequestInterface $request)
	{
        $driver = $this->getDriver();
        $stmt   = new PreparedStmt($driver->stmt_init());

        if (! $stmt->prepare($request->getSql())) {
            return $this->createResponse($stmt->getError());
        }

        /* normalize and bind parameters */
        if ($request->isValues()) {
            if (! $stmt->organizeParams($request->getValues())) {
                return $this->createResponse($stmt->getError());
            }
        }

        if (! $stmt->execute()) {
            return $this->createResponse($stmt->getError());
        }

        $isOrganized = $stmt->organizeResults();
        if (! $stmt->organizeResults()) {
            return $this->createResponse($stmt->getError());
        }

        /* database executed the query successfully and 
         * no results are needed
         */
        if ($isOrganized && ! $stmt->isResultset()) {
            return $this->createResponse();
        }

        $stmt->storeResults();

        $data = $stmt->fetch($request->getCallback());
        return $this->createResponse($data);
	}
}
