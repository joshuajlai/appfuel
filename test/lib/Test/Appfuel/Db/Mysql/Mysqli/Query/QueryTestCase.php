<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Db\Mysql\Mysqli\Query;

use Test\DbTestCase as ParentTestCase,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Mysqli\Query,
	Appfuel\Db\Mysql\Mysqli\Connection,
	mysqli;

/**
 * Handles common setup and tear downs. Should be extended by query tests
 */
class QueryTestCase extends ParentTestCase
{
	/**
	 * System under test
	 * @var MysqliQuery
	 */
	protected $query = null;

	/**
	 * Hold the connection details
	 * @var ConnectionDetail
	 */
	protected $conn = null;
	
	/**
	 * @var mysqli
	 */
	protected $handle = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->handle  = mysqli_init();
        $this->conn = new Connection($this->getConnDetail(), $this->handle);
        $this->assertTrue($this->conn->connect());

		$this->query = new Query($this->handle);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertTrue($this->conn->close());
		unset($this->conn);
		unset($this->query);
		unset($this->handle);
	}

    /**
     * @return array
     */
    public function sqlProviderQueryId_1()
    {  
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
        return array(
            array($sql)
        );
    }

    /**
     * @return array
     */
    public function sqlProviderQueryId_lt_4()
    {  
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id < 4';
        return array(
            array($sql)
        );
    }
}
