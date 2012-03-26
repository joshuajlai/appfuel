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
	Appfuel\DataSource\Db\DbRequestInterface,
	Appfuel\DataSource\Db\DbResponseInterface;

/**
 * The database adapter is 
 */
class PreparedStmtAdapter implements MysqliAdapterInterface
{
	/**
	 * @param	DbRequestInterface $request
	 * @return	DbReponseInterface
	 */
	public function execute(MysqliDriver $driver,
							DbRequestInterface $request,
							DbResponseInterface $response)
	{
        $stmt = new PreparedStmt($driver->stmt_init());
        $stmt->prepare($request->getSql());
        if ($stmt->isError()) {
			$error = $stmt->getError();
			$response->addError($error['error-text'], $error['error-nbr']);
			return $response;
        }

        /* normalize and bind parameters */
        if ($request->isValues()) {
            $stmt->organizeParams($request->getValues());
            if ($stmt->isError()) {
				$error = $stmt->getError();
				$response->addError($error['error-text'], $error['error-nbr']);
				return $response;
            }
        }

        $stmt->execute();
        if ($stmt->isError()) {
			$error = $stmt->getError();
			$response->addError($error['error-text'], $error['error-nbr']);
			return $response;
        }

        $isOrganized = $stmt->organizeResults();
        if ($stmt->isError()) {
			$error = $stmt->getError();
			$response->addError($error['error-text'], $error['error-nbr']);
            return $response;
        }

        /* database executed the query successfully and 
         * no results are needed
         */
        if ($isOrganized && ! $stmt->isResultset()) {
            return $response;
        }

        $stmt->storeResults();
		$errorStack = $response->getErrorStack();
        $data = $stmt->fetch($errorStack, $request->getCallback());
        
		if (is_array($data)) {
			$response->setResultSet($data);
		}
		
		return $response;
	}
}
