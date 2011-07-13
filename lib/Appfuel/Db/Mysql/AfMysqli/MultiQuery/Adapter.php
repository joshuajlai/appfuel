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
namespace Appfuel\Db\Mysql\AfMysqli\MultiQuery;

use Appfuel\Db\DbError,
	Appfuel\Db\Mysql\AfMysqli\AdapterBase,
	Appfuel\Framework\Db\Adapter\MultiQueryAdapterInterface,
	Appfuel\Framework\Db\Request\MultiQueryRequestInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class Adapter extends AdapterBase implements MultiQueryAdapterInterface
{
    /**
     * Excute a query represented by the sql.
     *
     * @param   MutliQueryRequestInterface  $request
     * @return  DbResponse
     */
    public function execute(MultiQueryRequestInterface $request)
    {
		$driver  = $this->getDriver();
		$stmt    = new Stmt();
		$sql     = $request->getSql();
		$options = $request->getResultOptions();

		$createResponse = array($this, 'createResponse');
		$data = $stmt->execute($driver, $sql, $createResponse, $options);

		return $this->createResponse($data);	
    }
}
