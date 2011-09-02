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
namespace TestFuel\Test\Db\Mysql\Mysqli\Prepared;

use mysqli,
    StdClass,
    mysqli_stmt,
    mysqli_result,
    TestFuel\TestCase\DbTestCase,
	Appfuel\Db\Request\PreparedRequest,
    Appfuel\Db\Mysql\AfMysqli\Connection,
    Appfuel\Db\Mysql\AfMysqli\PreparedStmt\Adapter;

/**
 * Test actual sql operations for prepared statements. Tests select,insert,
 * update and delete
 */
class AdapterTest extends DbTestCase
{

    /**
     * System under test
     * @var Server
     */
    protected $stmt = null;

    /**
     * Hold the connection
     * @var Connection
     */
    protected $conn = null;

    /**
     * Mysqli stmt resource handle 
     * @var mysqli_stmt
     */
    protected $adapter = null;

    /**
     * @return null
     */
    public function setUp()
    {  
        $this->conn = new Connection($this->getConnectionDetail());
        $this->assertTrue($this->conn->connect());

        $driver = $this->conn->getDriver();
        $this->adapter = new Adapter($driver);
    }

    /**
     * @return null
     */
    public function tearDown()
    {  
        $this->assertTrue($this->conn->close());
        unset($this->conn);
        unset($this->adapter);
    }

	/**
	 * @return array
	 */
	public function provideSqlQueryId_1()
	{
		$sql = 'SELECT query_id, result ' .
			   'FROM   test_queries ' .
			   'WHERE  query_id = ?';

		return array(
			array($sql, array(1))
		);
	}

	/**
	 * @dataProvider	provideSqlQueryId_1
	 * @return	null
	 */
	public function testSimpleSelectOneRow($sql, $values)
	{
		$request = new PreparedRequest('read');
		$request->setSql($sql)
				->setValues($values);

		$response = $this->adapter->execute($request);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$response
		);

		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertTrue($response->getStatus());
		$this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());

		$expected = array(
			'query_id'	=> 1,
			'result'	=> 'query issued'
		);
		$this->assertEquals($expected, $response->getCurrentResult());
		$this->assertEquals(array($expected), $response->getResultset());
	}

	/**
	 * @return	null
	 */
	public function testExecuteFailedPrepare()
	{
		$sql = 'SELECT query_id FROM _blah_no_table_';
		$values = array();
		$request = new PreparedRequest('read');
		$request->setSql($sql)
				->setValues($values);

		$response = $this->adapter->execute($request);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$response
		);

		$this->assertFalse($response->isSuccess());
		$this->assertTrue($response->isError());
		$this->assertFalse($response->getStatus());
		$this->assertEquals(0, $response->getRowCount());
		
		$error = $response->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);
			
		$this->assertEquals(1146, $error->getCode());
		$this->assertEquals('42S02', $error->getSqlState());
	}
}
