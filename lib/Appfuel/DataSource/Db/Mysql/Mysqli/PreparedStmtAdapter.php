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
