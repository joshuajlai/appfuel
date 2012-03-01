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
	Appfuel\DataSource\Db\Mysql\Mysqli\QueryAdapter;

/**
 */
class QueryAdapterTest extends BaseTestCase
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
	 * @var QueryAdapter
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
					  WHERE  query_id IN(1,2,3)";

		$this->conn->connect();
		$this->adapter = new QueryAdapter();
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
	public function getQueryAdapter()
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
		$adapter = $this->getQueryAdapter();
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
		$adapter = $this->getQueryAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);
		$request->enableResultBuffer();
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertEquals(3, $response->count());
		
		$expected = array(
			array('param_2' => 'code_a', 'result' => 'query issued'),
			array('param_2' => 'code_b', 'result' => 'query 2 issued'),
			array('param_2' => 'code_c', 'result' => 'query 3 issued'),
		);
		
		$this->assertEquals($expected, $response->getResultSet());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	position (MYSQLI_NUM)
	 * DbRequest::getCallback		null
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileB()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getQueryAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);
		$request->setResultType('position');

		$request->enableResultBuffer();
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertEquals(3, $response->count());
	
		$expected = array(
			array(0 => 'code_a', 1 => 'query issued'),
			array(0 => 'code_b', 1 => 'query 2 issued'),
			array(0 => 'code_c', 1 => 'query 3 issued'),
		);
		
		$this->assertEquals($expected, $response->getResultSet());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	name-pos (MYSQLI_BOTH)
	 * DbRequest::getCallback		null
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileC()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getQueryAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);
		$request->setResultType('name-pos');

		$request->enableResultBuffer();
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertEquals(3, $response->count());
	
		$expected = array(
			array(
				0		    => 'code_a', 
				'param_2'	=> 'code_a',
				1			=> 'query issued',
				'result'	=> 'query issued',
			),
			array(
				0			=> 'code_b', 
				'param_2'	=> 'code_b',
				1			=> 'query 2 issued',
				'result'	=> 'query 2 issued'
			),
			array(
				0			=> 'code_c', 
				'param_2'	=> 'code_c',
				1			=> 'query 3 issued',
				'result'	=> 'query 3 issued',
			),
		);
		
		$this->assertEquals($expected, $response->getResultSet());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	name (MYSQLI_ASSOC)
	 * DbRequest::getCallback		map column names
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileD()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getQueryAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);

		$func = function ($row) {
			return array(
				'new_param_2' => $row['param_2'],
				'new_result'  => $row['result']
			);
		};
		
		$request->setCallback($func);
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertEquals(3, $response->count());
	
		$expected = array(
			array('new_param_2' => 'code_a', 'new_result' => 'query issued'),
			array('new_param_2' => 'code_b', 'new_result' => 'query 2 issued'),
			array('new_param_2' => 'code_c', 'new_result' => 'query 3 issued'),
		);
		
		$this->assertEquals($expected, $response->getResultSet());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	name (MYSQLI_ASSOC)
	 * DbRequest::getCallback		null
	 * 
	 * using invalid sql: table will not exist
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileE()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getQueryAdapter();
		$sql     = "SELECT * FROM my_table_does_not_exist";
		$request = new DbRequest($sql);

		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertTrue($response->isError());

		$errorStack = $response->getErrorStack();
		$this->assertEquals(2, $errorStack->count());

		$error = $errorStack->current();
		$this->assertEquals(500, $error->getCode());
		$msg = get_class($adapter) . " expected mysqli_result none given";
		$this->assertEquals($msg, $error->getMessage());

		$errorStack->next();
		$error = $errorStack->current();
		$this->assertEquals(1146, $error->getCode());

		$msg  = "Table 'af_unittest.my_table_does_not_exist' ";
		$msg .= "doesn't exist 42S02";

		$this->assertEquals($msg, $error->getMessage());
	}

	/**
	 * DbRequest::isResultBuffer:	true
	 * DbRequest::getResultType:	name (MYSQLI_ASSOC)
	 * DbRequest::getCallback		throws an exception
	 * 
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testExecuteProfileF()
	{
		$driver  = $this->getDriver();
		$adapter = $this->getQueryAdapter();
		$sql     = $this->getSql();
		$request = new DbRequest($sql);

		$errMsg = "error as occured";
		$func = function ($row) use ($errMsg) {
			throw new Exception($errMsg, 99);
		};
		$request->setCallback($func);
		$response = new DbResponse();

		$result = $adapter->execute($driver, $request, $response);
		$this->assertSame($response, $result);
		$this->assertTrue($response->isError());
		
		$stack = $response->getErrorStack();
		$this->assertEquals(1, $stack->count());
		$error = $stack->current();
	
		$class = get_class($adapter);
		$expected  = "failed result fetch from {$class}: $errMsg";
		$expected .= " at row index -(0)";

		$this->assertEquals(99, $error->getCode());
		$this->assertEquals($expected, $error->getMessage());
	}


}
