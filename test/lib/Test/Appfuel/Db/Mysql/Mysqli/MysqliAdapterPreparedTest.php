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
namespace Test\Appfuel\Db\Mysql\Mysqli;

use Test\DbTestCase as ParentTestCase,
	Appfuel\Db\Mysql\Mysqli\Connection,
	Appfuel\Db\Mysql\Mysqli\Adapter;

/**
 * Callback used in test below
 * 
 * @param	array	$row	raw record
 * @return	array
 */
function myMapper(array $row)
{
	$data = array();
	if (isset($row['query_id'])) {
		$data['myQueryId'] = $row['query_id'];
	}

	if (isset($row['result'])) {
		$data['myResult'] = $row['result'];
	}

	return $data;
}


/**
 */
class MysqliAdapterPrepareTest extends ParentTestCase
{
	protected $adapter = null;
	protected $handle = null;
	protected $conn = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->handle  = mysqli_init();
        $this->conn = new Connection($this->getConnDetail(), $this->handle);
		$this->assertTrue($this->conn->connect());

		$this->adapter = new Adapter($this->handle);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertTrue($this->conn->close());
		unset($this->handle);
		unset($this->conn);
		unset($this->adapter);
	}

    /**
     * @return array
     */
    public function providePreparedSelectSql()
    {  
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id=?';
        return array(
            array($sql, array(2))
        );
    }

    /**
     * @dataProvider    providePreparedSelectSql
	 * @expectedException	Exception
     * @return null
     */
    public function testExecutePreparedStmtNotConnected($sql, $values)
    {  
		$this->assertTrue($this->conn->close());
        $response = $this->adapter->executePreparedStmt($sql, $values);
    }


    /**
     * @return null
     */
    public function testExecutePreparedStmtFailedPrepared()
    {
        $sql = 'SELECT query_id, result FROM does_not_exist WHERE query_id=?';
        $values = array(1);
        $response = $this->adapter->executePreparedStmt($sql, $values);

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isError());

        $error = $response->getError();
        $this->assertInstanceOf(
            'Appfuel\Db\DbError',
            $error
        );

        /* mysql error code for table does not exist */
        $this->assertEquals(1146, $error->getCode());

        $errMsg = "Table 'af_unittest.does_not_exist' doesn't exist";
        $this->assertEquals($errMsg, $error->getMessage());
        $this->assertEquals('42S02', $error->getSqlState());
    }

    /**
     * @dataProvider    providePreparedSelectSql
     * @return null
     */
    public function testExecutePreparedStmtValid($sql, $values)
    {
        $response = $this->adapter->executePreparedStmt($sql, $values);

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
        $this->assertEquals(1, $response->getRowCount());

        $expected = array(
            'query_id'  => 2,
            'result'    => 'query 2 issued'
        );
        $this->assertEquals(1, $response->getRowCount());
        $this->assertEquals($expected, $response->getCurrentResult());
    }
}
