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
namespace Appfuel\Db\Mysql\Mysqli\PreparedStmt;

use mysqli,
	Appfuel\Db\Mysql\Mysqli\AdapterBase,
	Appfuel\Framework\Db\Adapter\PreparedAdapterInterface,
	Appfuel\Framework\Db\Request\PreparedRequestInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class Adapter extends AdapterBase implements PreparedAdapterInterface
{
    /**
     * Excute a query represented by the sql.
     *
     * @param   string  $sql
     * @param   string  $type 
     * @param   bool    $isBuffered
     * @param   mixed   $filter
     * @return  DbResponse
     */
    public function execute(PreparedRequestInterface $request)
    {
		$driver = $this->getDriver();
		$stmt   = new Stmt($driver->stmt_init());
	        
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
		return $this->createResponse($data);;
    }
}
