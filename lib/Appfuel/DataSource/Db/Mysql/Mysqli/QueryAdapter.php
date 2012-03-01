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
	Appfuel\DataSource\Db\DbResponse,
	Appfuel\DataSource\Db\DbAdapterInterface,
	Appfuel\DataSource\Db\DbRequestInterface;

/**
 * The database adapter is 
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
			$this->addError($e->getMessage(), $e->getCode());
			return false;
        }

        if (! ($resultHandle instanceof mysqli_result)) {
			$this->addError("result not instanceof mysqli_result");
			return false;
		}
            
		$result = new QueryResult($resultHandle);
        $data = $resultSet->fetchAllData($type, $filter);
        
        if (false === $data) {
			$errText = "{$driver->error} {$driver->sqlstate}";
			$errCode = $driver->errno;
			$this->addError($errText, $errCode);
			return false;
        } else if (true === $data) {
            $data = null;
        }

		$response->load($data);
		return $response;;
	}
}
