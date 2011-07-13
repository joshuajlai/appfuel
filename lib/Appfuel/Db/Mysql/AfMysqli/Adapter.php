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
namespace Appfuel\Db\Mysql\AfMysqli;

use mysqli,,
	Appfuel\Db\DbResponse,
	Appfuel\Framework\Exception;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class Adapter
{
	/**
	 * Mysqli driver used create prepared stmt and queries
	 * @var mysqli
	 */
	protected $driver = null;

	/**
	 * Error value object containing the last know error
	 * @var ErrorInterface
	 */
	protected $error = null;

	/**
	 * @param	ConnectionDetail	$detail
	 * @return	Adapter
	 */
	public function __construct(mysqli $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return	Query\MysqliQuery
	 */
	public function createQuery()
	{
		return new Query($this->getDriver());
	}

	/**
	 * @return	Query\MysqliQuery
	 */
	public function createPreparedStmt()
	{
		$driver = $this->getDriver();
		return new Prepared\Stmt($driver->stmt_init());
	}

	/**
	 * @return	Server
	 */
	protected function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @return	bool
	 */
	protected function isDriver()
	{
		return $this->driver instanceof mysqli;
	}

    /**
     * Excute a query represented by the sql.
     *
     * @param   string  $sql
     * @param   string  $type 
     * @param   bool    $isBuffered
     * @param   mixed   $filter
     * @return  DbResponse
     */
    public function executeMultiQuery(MultiQueryList $queryList)
    {  

        $query = $this->createQuery();
		$sql   = $queryList->concatenateSql();
		$keys  = $queryList->createOptions();

		$data = $query->executeMultiQuery($sql, $options);
		 
        return new DbResponse($status, $data, $error);
    }


    /**
     * Excute a query represented by the sql.
     *
     * @param   string  $sql
     * @param   string  $type 
     * @param   bool    $isBuffered
     * @param   mixed   $filter
     * @return  DbResponse
     */
    public function executeQuery($sql,
                                 $resultType = 'name',
                                 $isBuffered = true,
                                 $filter     = null)
    {  

        $query = $this->createQuery();
        $isBuffered =(bool) $isBuffered;
        $resultMode = MYSQLI_STORE_RESULT;
        if (! $isBuffered) {
            $resultMode = MYSQLI_USE_RESULT;
        }

        switch ($resultType) {
            /* column names as keys in the result */
            case 'name' :
                $resultType = MYSQLI_ASSOC;
                break;
            /* column position as keys in the result */
            case 'position':
                $resultType = MYSQLI_NUM;
                break;
            case 'both':
                $resultType = MYSQLI_BOTH;
                break;
            default:
                $resultType = MYSQLI_ASSOC;
        }

        return $query->execute($sql, $resultMode, $resultType, $filter);
    }

    /**
     * @param   string  $sql
     * @param   array   $values 
     * @param   bool    $isBuffered
     * @param   mixed   $filter
     * @return  DbResponse
     */
    public function executePreparedStmt($sql,
                                        array $values = null,
                                        $filter       = null)
    {
        $stmt = $this->createPreparedStmt();
        if (! $stmt->prepare($sql)) {
            return new DbResponse(null, $stmt->getError());
        }

        /* normalize and bind parameters */
        if (is_array($values) && ! empty($values)) {
            if (! $stmt->organizeParams($values)) {
                return new DbResponse(null, $stmt->getError());
            }
        }

        if (! $stmt->execute()) {
            return new DbResponse(null, $stmt->getError());
        }

        $isOrganized = $stmt->organizeResults();
        if (! $stmt->organizeResults()) {
            return new DbResponse(null, $stmt->getError());
		}

		/* database executed the query successfully and 
         * no results are needed
         */
        if ($isOrganized && ! $stmt->isResultset()) {
            return new DbResponse();
        }

        $stmt->storeResults();

        $data = $stmt->fetch($filter);
        if ($data instanceof ErrorInterface) {
            return new DbResponse(null, $data);
        }

        $stmt->freeStoredResults();
        return new DbResponse($data);
	}
}
