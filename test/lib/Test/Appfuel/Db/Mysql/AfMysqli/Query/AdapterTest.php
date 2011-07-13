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
namespace Test\Appfuel\Db\Mysql\AfMysqli\Query;

use mysqli,
    Test\DbCase as ParentTestCase,
	Appfuel\Db\Request\QueryRequest,
    Appfuel\Db\Connection\ConnectionDetail,
    Appfuel\Db\Mysql\AfMysqli\Connection,
    Appfuel\Db\Mysql\AfMysqli\Query\Adapter;

/**
 */
class AdapterTest extends ParentTestCase
{
    /**
     * System under test
     * @var Adapter
     */
    protected $adapter = null;

    /**
     * Hold the connection details
     * @var ConnectionDetail
     */
    protected $conn = null;

    /**
     * @return null
     */
    public function setUp()
    {  
        $this->conn = new Connection($this->getConnDetail());
        $this->assertTrue($this->conn->initialize());
        $this->assertTrue($this->conn->connect());

        $this->adapter = new Adapter($this->conn->getDriver());
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

	public function sqlProviderQuery1()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		return array(
			array($sql)
		);
	}

	/**
	 * Test the adapter execute with default values
	 *
	 * @dataProvider	sqlProviderQuery1
	 * @return null
	 */
	public function testExecuteDefaults($sql)
	{
		$request = new QueryRequest();
		$request->setSql($sql);
		
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
			'query_id' => 1,
			'result'   => 'query issued'
		);
		$this->assertEquals($expected, $response->getCurrentResult());
		$this->assertEquals(array($expected), $response->getResultset());
	}

	/**
	 * Test the adapter execute with resultset as postional not column names
	 *
	 * @dataProvider	sqlProviderQuery1
	 * @return null
	 */
	public function testExecuteResultTypePosition($sql)
	{
		$request = new QueryRequest();
		$request->setSql($sql);
		$request->setResultType('position');		
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
			0 => 1,
			1 => 'query issued'
		);
		$this->assertEquals($expected, $response->getCurrentResult());
		$this->assertEquals(array($expected), $response->getResultset());
	}

	/**
	 * Test the adapter execute with result having both positional and 
	 * column names
	 *
	 * @dataProvider	sqlProviderQuery1
	 * @return null
	 */
	public function testExecuteResultTypePositionAndColumnNames($sql)
	{
		$request = new QueryRequest();
		$request->setSql($sql);
		$request->setResultType('both');		
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
			0			=> 1,
			'query_id'	=> 1,
			1			=> 'query issued',
			'result'	=> 'query issued'
			
		);
		$this->assertEquals($expected, $response->getCurrentResult());
		$this->assertEquals(array($expected), $response->getResultset());
	}

	/**
	 * @dataProvider	sqlProviderQuery1
	 * @return null
	 */
	public function testExecuteCallbacks($sql)
	{
		$callback = function ($row) {
			$data = array();
			if (isset($row['query_id'])) {
				$data['id'] = $row['query_id'];
			}
			if (isset($row['result'])) {
				$data['myResult'] = $row['result'];
			}

			return $data;
		};

		$request = new QueryRequest();
		$request->setSql($sql);
		$request->setCallback($callback);
		
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
			'id'		=> 1,
			'myResult'	=> 'query issued' 
		);
		$this->assertEquals($expected, $response->getCurrentResult());
		$this->assertEquals(array($expected), $response->getResultset());
	}

	/**
	 * @return null
	 */
	public function testEmptySql()
	{
		$request = new QueryRequest();
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
		$expected = 'AF_ERR_MYSQLI_QUERY_STMT';
		$this->assertEquals($expected, $error->getCode());
		
		$expected = 'Invalid execute: sql is empty';
		$this->assertEquals($expected, $error->getMessage());
		$this->assertNull($error->getSqlState());
	}

	/**
	 * @return null
	 */
	public function testSelectNoTableSql()
	{
		$sql = 'SELECT query_id FROM no_such_table WHERE query_id=1';
		$request = new QueryRequest();
		$request->setSql($sql);

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
	}


}
