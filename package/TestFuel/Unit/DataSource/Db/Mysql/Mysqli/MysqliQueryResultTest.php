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
	Appfuel\Error\ErrorStack,
	Appfuel\DataSource\Db\DbRegistry,
	Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn,
	Appfuel\DataSource\Db\Mysql\Mysqli\QueryResult;

/**
 */
class MysqliQueryResultTest extends BaseTestCase
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
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->conn->close();
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
	public function testFree()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_STORE_RESULT);
		$result = new QueryResult($handle);
		$this->assertTrue($result->isHandle());
		$this->assertNull($result->free());
		$this->assertFalse($result->isHandle());
	}

	/**
	 * @return	null
	 */
	public function testGetColumnNames()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_STORE_RESULT);
		$result = new QueryResult($handle);
		$data = $result->getColumnNames();
		$expected = array('param_2', 'result');
		$this->assertEquals($expected, $data);
	}

	/**
	 * @return	null
	 */
	public function testFetchAllDataStoreResultAssoc()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_STORE_RESULT);
		$result = new QueryResult($handle);
		$this->assertSame($handle, $result->getHandle());
		$this->assertTrue($result->isHandle());

		$errorStack = new ErrorStack();
		$data = $result->fetchAllData($errorStack, MYSQLI_ASSOC);
		$expected = array(
			array('param_2' => 'code_a', 'result' => 'query issued'),
			array('param_2' => 'code_b', 'result' => 'query 2 issued'),
			array('param_2' => 'code_c', 'result' => 'query 3 issued'),
		);
		$this->assertEquals(0, $errorStack->count());
		$this->assertEquals($expected, $data);
		$this->assertFalse($result->isHandle());
	}

	/**
	 * @return	null
	 */
	public function testFetchAllDataStoreResultNum()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_STORE_RESULT);
		$result = new QueryResult($handle);
		$this->assertSame($handle, $result->getHandle());
		$this->assertTrue($result->isHandle());

		$errorStack = new ErrorStack();
		$data = $result->fetchAllData($errorStack, MYSQLI_NUM);
		$expected = array(
			array(0 => 'code_a', 1 => 'query issued'),
			array(0 => 'code_b', 1 => 'query 2 issued'),
			array(0 => 'code_c', 1 => 'query 3 issued'),
		);
		$this->assertEquals(0, $errorStack->count());
		$this->assertEquals($expected, $data);
		$this->assertFalse($result->isHandle());
	}

	/**
	 * @return	null
	 */
	public function testFetchAllDataStoreResultBoth()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_STORE_RESULT);
		$result = new QueryResult($handle);
		$this->assertSame($handle, $result->getHandle());
		$this->assertTrue($result->isHandle());

		$errorStack = new ErrorStack();
		$data = $result->fetchAllData($errorStack, MYSQLI_BOTH);
		$expected = array(
			array(
				0 => 'code_a', 
				'param_2' => 'code_a', 
				1 =>'query issued', 
				'result' => 'query issued'
			),
			array(
				0 => 'code_b', 
				'param_2' => 'code_b', 
				1 =>'query 2 issued', 
				'result' => 'query 2 issued'
			),
			array(
				0 => 'code_c', 
				'param_2' => 'code_c', 
				1 =>'query 3 issued', 
				'result' => 'query 3 issued'
			),
		);
		$this->assertEquals(0, $errorStack->count());
		$this->assertEquals($expected, $data);
		$this->assertFalse($result->isHandle());
	}

	/**
	 * @return	null
	 */
	public function testFetchAllDataUseResultAssoc()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_USE_RESULT);
		$result = new QueryResult($handle);
		
		$this->assertSame($handle, $result->getHandle());
		$this->assertTrue($result->isHandle());

		$errorStack = new ErrorStack();
		$data = $result->fetchAllData($errorStack, MYSQLI_ASSOC);
		$expected = array(
			array('param_2' => 'code_a', 'result' => 'query issued'),
			array('param_2' => 'code_b', 'result' => 'query 2 issued'),
			array('param_2' => 'code_c', 'result' => 'query 3 issued'),
		);
		$this->assertEquals(0, $errorStack->count());
		$this->assertEquals($expected, $data);
		$this->assertFalse($result->isHandle());
	}

	/**
	 * @return	null
	 */
	public function testFilterResultCallBack()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_USE_RESULT);
		$result = new QueryResult($handle);

		$errorStack = new ErrorStack();
		$expected = array(
			array(
				'mapped_param' => 'code_a', 
				'mapped_result' => 'query issued'
			),
			array(
				'mapped_param' => 'code_b', 
				'mapped_result' => 'query 2 issued'
			),
			array(
				'mapped_param' => 'code_c', 
				'mapped_result' => 'query 3 issued'
			)
		);

		$func = function($row) {
			return array(
				'mapped_param'  => $row['param_2'],
				'mapped_result' => $row['result']
			);
		};

		$data = $result->fetchAllData($errorStack, MYSQLI_ASSOC, $func);
		$this->assertSame($expected, $data);
	}

	/**
	 * @return	null
	 */
	public function testFilterResultCallbackThrowsException()
	{
		$driver = $this->getDriver();
		$sql    = $this->getSql();
		$handle = $driver->query($sql, MYSQLI_USE_RESULT);
		$result = new QueryResult($handle);

		$errorStack = new ErrorStack();
		$result = new QueryResult($handle);

		$errMsg = 'my error';
		$func = function($row) use($errMsg) {
			throw new Exception($errMsg, 544);
		};

		$data = $result->fetchAllData($errorStack, MYSQLI_ASSOC, $func);
		$this->assertEquals(3, $errorStack->count());

		$expected = "{$errMsg} -(0)";
		$error = $errorStack->current();
		$this->assertEquals($expected, $error->getMessage());

		$errorStack->next();
		$expected = "{$errMsg} -(1)";
		$error = $errorStack->current();
		$this->assertEquals($expected, $error->getMessage());

		$errorStack->next();
		$expected = "{$errMsg} -(2)";
		$error = $errorStack->current();
		$this->assertEquals($expected, $error->getMessage());
	}
}
