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
namespace TestFuel\Unit\DataSource\Db\Mysql\Mysqli;

use StdClass,
	Exception,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\DbRegistry,
	Appfuel\DataSource\Db\DbRequest,
	Appfuel\DataSource\Db\DbResponse,
	Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn,
	Appfuel\DataSource\Db\Mysql\Mysqli\MultiQueryAdapter;

/**
 */
class MultiQueryAdapterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MysqliConn
	 */
	protected $conn = null;

	/**
	 * Parameters used to connect to the database
	 * @var array
	 */
	protected $params = null;

	/**
	 * System under test
	 * @var MultiQueryAdapter
	 */
	protected $adapter = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->runDbStartupTask();
		$connector = DbRegistry::getConnector('af-tester');
		$this->conn = $connector->getConnection();
		$this->sql = "SELECT param_2, result 
					  FROM   test_queries 
					  WHERE  query_id = 1;

					  SELECT param_2, result 
					  FROM   test_queries 
					  WHERE  query_id = 2;
					
					  SELECT param_2, result 
					  FROM   test_queries 
					  WHERE  query_id = 3";

		$this->conn->connect();
		$this->adapter = new MultiQueryAdapter();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->conn->close();
		$this->conn = null;
		$this->adapter = null;
	}

	/**
	 * @return	QueryAdapter
	 */	
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @return	mysqli
	 */
	public function getDriver()
	{
		return $this->conn->getDriver();
	}
	
	/**
	 * @return	string
	 */
	public function getSql()
	{
		return $this->sql;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$adapter = $this->getAdapter();
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliAdapterInterface',
			$adapter
		);
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	name (MYSQLI_ASSOC)
	 * DbRequest::getCallback		null
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileA()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertEquals(3, $response->count());

		$expected = new DbResponse();
		$expected->setResultSet(array(
			array('param_2' => 'code_a', 'result' => 'query issued')
		));
		$this->assertEquals($expected, $response->current());

		$expected->setResultSet(array(
			array('param_2' => 'code_b', 'result' => 'query 2 issued'),
		));
		$response->next();
		$this->assertEquals($expected, $response->current());

		$expected->setResultSet(array(
			array('param_2' => 'code_c', 'result' => 'query 3 issued'),
		));
		$response->next();
		$this->assertEquals($expected, $response->current());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getCallback		map column names
	 * DbRequest::setMultiResultOptions
	 *  
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileB()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);

		$func1 = function ($row) {
			return array(
				'a_col_1' => $row['param_2'],
				'a_col_2' => $row['result']
			);
		};

		$func2 = function ($row) {
			return array(
				'b_col_1' => $row['param_2'],
				'b_col_2' => $row['result']
			);
		};

		$func3 = function ($row) {
			return array(
				'c_col_1' => $row['param_2'],
				'c_col_2' => $row['result']
			);
		};

		$options = array(
			array('result-key' => 'first_query',  'callback' => $func1),
			array('result-key' => 'second_query', 'callback' => $func2),
			array('result-key' => 'third_query',  'callback' => $func3)
		);

		
		$request->setMultiResultOptions($options);
		
		$response = new DbResponse();
		$return = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $return);
		$this->assertEquals(3, $response->count());
		
		$result = $response->getResult('first_query');
		$expected = new DbResponse();
		$expected->setResultSet(array(
			array('a_col_1' => 'code_a', 'a_col_2' => 'query issued')
		));	
		$this->assertEquals($expected, $result);
	
		$result = $response->getResult('second_query');
		$expected->setResultSet(array(
			array('b_col_1' => 'code_b', 'b_col_2' => 'query 2 issued')
		));	
		$this->assertEquals($expected, $result);

		$result = $response->getResult('third_query');
		$expected->setResultSet(array(
			array('c_col_1' => 'code_c', 'c_col_2' => 'query 3 issued')
		));	
		$this->assertEquals($expected, $result);
	}

	/**
	 * In this test the middle query is faulty. Things to note: 
	 * 1) only 2 responses are in the main response the third one could not
	 *    be processed because of the error in the second
	 * 2) The main response has a copy of the error item in the second response
	 *    error stack
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileE()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getAdapter();
		$sql     = "SELECT param_2, result 
                      FROM   test_queries 
                      WHERE  query_id = 1;

                      SELECT param_2, result 
                      FROM   no_table_exists 
                      WHERE  query_id = 2;
                    
                      SELECT param_2, result 
                      FROM   test_queries 
                      WHERE  query_id = 3";

		$request = new DbRequest($sql);
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertTrue($response->isError());

		$errorStack = $response->getErrorStack();
		$this->assertEquals(1, $errorStack->count());

		$error = $errorStack->current();
		$this->assertEquals(1146, $error->getCode());
		$expected  = "1:Table 'af_unittest.no_table_exists' ";
		$expected .= "doesn't exist:42S02";

		$this->assertEquals($expected, $error->getMessage());

		$rs = $response->getResult(1);
		$this->assertTrue($rs->isError());
		
		$rsError = $rs->getError();
		$this->assertSame($error, $rsError);
	}
}
