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
namespace Appfuel\Db\Mysql\Mysqli\Query;

use Appfuel\Db\DbError,
	Appfuel\Db\DbResponse,
	Appfuel\Db\Mysql\Mysqli\AdapterBase,
	Appfuel\Framework\Db\Request\RequestInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class Adapter extends AdapterBase implements QueryAdapterInterface
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
    public function execute(RequestInterface $request)
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
            case 'both':
                $type = MYSQLI_BOTH;
                break;
            default:
                $type = MYSQLI_ASSOC;
        }

        $stmt   = new Stmt();
		$sql    = $request->getSql();
		$filter = $request->getCallback();
		
		$driver = $this->getDriver();
        $data   = $stmt->execute($driver, $sql, $mode, $type, $filter);
		
		if (false === $data) {
			$data = new DbError(
				$driver->errno, 
				$driver->error, 
				$driver->sqlstate
			);
		} elseif (true === $data) {
			$data = null;
		}

		return $this->createResponse($data);
    }
}
