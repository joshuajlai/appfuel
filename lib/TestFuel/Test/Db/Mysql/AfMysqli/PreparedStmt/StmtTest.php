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
namespace TestFuel\Test\Db\Mysql\AfMysqli\PreparedStmt;

use mysqli,
	StdClass,
    mysqli_stmt,
    mysqli_result,
	TestFuel\TestCase\DbTestCase,
    Appfuel\Db\Mysql\AfMysqli\Connection,
    Appfuel\Db\Mysql\AfMysqli\PreparedStmt\Stmt;

/**
 * Test the ability to use a mysqli_stmt to performe a query with prepared
 * statements
 */
class StmtTest extends DbTestCase
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
    protected $stmtDriver = null;

    /**
     * @return null
     */
    public function setUp()
    {  
        $this->conn = new Connection($this->getConnectionDetail());
		$this->assertTrue($this->conn->initialize());
        $this->assertTrue($this->conn->connect());

		$driver = $this->conn->getDriver();
        $this->stmtDriver = $driver->stmt_init();
        $this->stmt = new Stmt($this->stmtDriver);
    }

    /**
     * @return null
     */
    public function tearDown()
    {  
        $this->assertTrue($this->conn->close());
        unset($this->conn);
        unset($this->driver);
        unset($this->stmtDriver);
        unset($this->stmt);

    }

	/**
	 * Reuse a simple prepared sql for testing the basic operations of 
	 * the prepared stat PreparedStmt class
	 *
	 * @return array
	 */
	public function provideBasicSql()
	{
		$sql = 'SELECT	query_id
				,		param_1
				,		param_2
				,		param_3
				,		result
				FROM	test_queries 
				WHERE	query_id = ?';

		$values = array(1);

		return array(
			array($sql, $values)
		);
	}

	/**
	 * @return array
	 */
	public function provideManyRowsSql()
	{
		$sql = 'SELECT	query_id
				,		param_1
				,		param_2
				,		param_3
				,		result
				FROM	test_queries';

		$values = array();

		return array(
			array($sql, $values)
		);
	}


	/**
	 * Reuse a simple prepared sql for testing the basic operations of 
	 * the prepared stat PreparedStmt class. This sql will cause a mysql 
	 * error no   : 1146  
	 * error msg  : Table 'table_does_not_exit' doesn't exist
	 * sql state  : 42S02
	 *
	 * @return array
	 */
	public function provideInvalidSql()
	{
		$sql = 'SELECT	query_id
				FROM	table_does_not_exist
				WHERE	query_id = ?';

		$values = array(1);

		return array(
			array($sql, $values)
		);
	}

	/**
	 * @return array
	 */
	public function provideNormalizeParamsExpectations()
	{
		return array(
			array(array(), array()),
			array(array(1), array('s', 1)),
			array(array(1,2), array('ss', 1, 2)),
			array(array(1,2, 1.2), array('sss', 1, 2, 1.2)),
			array(array('a', 'b', 'c', 'd'), array('ssss', 'a','b','c','d')),
			array(array(1,2,3,4,5), array('sssss', 1,2,3,4,5)),
			array(array(1, array(1,2,3), 4), array('sssss', 1,1,2,3,4)),
			array(array(array(1,2,3),array(),1), array('ssss', 1,2,3,1)),
			array(array(array(), array(), array()), array()),
			array(array(array(), array(), array(),1), array('s', 1))
		);
	}


	/**
	 * The handle is made immutable by passing it through the constructor
	 *
	 * @return	null
	 */
	public function testGetIsDriver()
	{
		$result = $this->stmt->getDriver();
		$this->assertInstanceOf('mysqli_stmt', $result);
		$this->assertSame($this->stmtDriver, $result);
		$this->assertTrue($this->stmt->isDriver());
	}

	/**
	 * Closing the stmt before prepare causes an error which is caught 
	 * and handled.
	 *
	 * @return null
	 */
	public function testCloseBeforePrepare()
	{
		$this->assertTrue($this->stmt->isDriver());
		$this->assertFalse($this->stmt->close());
		$this->assertTrue($this->stmt->isError());

		$error = $this->stmt->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);

		/* for whatever reason mysql return error code 2 when used before
		 * the prepared
		 */
		$this->assertEquals(2, $error->getCode());
		
		$this->assertEquals(
			'mysqli_stmt::close(): invalid object or resource mysqli_stmt',
			$error->getMessage()
		);

		$this->assertFalse($this->stmt->isDriver());
		$this->assertNull($this->stmt->getDriver());
	}


	/**
	 * @dataProvider provideBasicSql 
	 */
	public function testCloseAfterPrepare($sql, $value)
	{
		$this->assertTrue($this->stmt->isDriver());
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->close());
		$this->assertTrue($this->stmt->isClosed());
		$this->assertFalse($this->stmt->isError());
		$this->assertFalse($this->stmt->isDriver());
		$this->assertNull($this->stmt->getDriver());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider	provideBasicSql 
	 */
	public function testCloseAfterClose($sql)
	{
		$this->assertTrue($this->stmt->isDriver());
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->close());
		$this->assertTrue($this->stmt->isClosed());
	
		/* will throw an exception no handle given */	
		$this->stmt->close();
		
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testCloseAfterCloseBeforePrepare()
	{
		$this->assertTrue($this->stmt->isDriver());
		$this->assertFalse($this->stmt->close());
		$this->assertFalse($this->stmt->isClosed());
	
		/* will throw an exception no handle given */	
		$this->stmt->close();
	}

	/**
	 * @dataProvider	provideBasicSql
	 * @return null
	 */
	public function testPrepare($sql, $values)
	{
		$this->assertFalse($this->stmt->isPrepared());
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());
		$this->assertFalse($this->stmt->isError());	
		$this->assertTrue($this->stmt->close());
	}

	/**
	 * @dataProvider	provideInvalidSql
	 * @return null
	 */
	public function testPrepareInvalidSql($sql, $values)
	{
		$this->assertFalse($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isError());
		
		$error = $this->stmt->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);

		$this->assertEquals(1146, $error->getCode());
		$this->assertEquals('42S02', $error->getSqlState());

		$expected = "Table 'af_unittest.table_does_not_exist' doesn't exist";
		$this->assertEquals($expected, $error->getMessage());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testPrepareSqlIsEmpty()
	{
		$this->stmt->prepare('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testPrepareSqlIsInt()
	{
		$this->stmt->prepare(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testPrepareSqlIsArray()
	{
		$this->stmt->prepare(array(1,3,4,5));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testPrepareSqlIsObject()
	{
		$this->stmt->prepare(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testPrepareAfterClose($sql, $value)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->close());
		$this->assertTrue($this->stmt->isClosed());
		
		$this->stmt->prepare($sql);
	}

	/**
	 * This algorithm produces an array where the first element is a 
	 * string where each character 's' represents an element in the array
	 * passed in. When an element is an array that array is flattened out
	 *
	 * @dataProvider	provideNormalizeParamsExpectations
	 * @param	null
	 */
	public function testNormalizeParams($params, $expected)
	{
		$this->assertEquals($expected, $this->stmt->normalizeParams($params));
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testBindParams($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));

		/* this is required to transform the sql values into an 
		 * array mysqli_stmt::bind_params can use vi call_user_func_array
		 */
		$params = $this->stmt->normalizeParams($values);
		$this->assertTrue($this->stmt->bindParams($params));
		$this->assertTrue($this->stmt->isParamsBound());
		$this->stmt->close();
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testBindParamsLessThan2($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		
		$this->assertFalse($this->stmt->bindParams(array()));	
		$this->assertTrue($this->stmt->isError());
		$this->assertFalse($this->stmt->isParamsBound());

		$error = $this->stmt->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);

		$this->assertEquals(10000, $error->getCode());
		$this->assertNull($error->getSqlState());
		$this->assertEquals(
			'bindParams fail: invalid args passed in',
			$error->getMessage()
		);


		$this->assertFalse($this->stmt->bindParams(array('s')));
		$this->assertTrue($this->stmt->isError());
		$this->assertEquals($error, $this->stmt->getError());
		$this->assertFalse($this->stmt->isParamsBound());

		$this->stmt->close();
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testBindParamsKeysNotSet($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		
		$badParams = array('a' => 's', 'b' => 1);
		$this->assertFalse($this->stmt->bindParams($badParams));	
		$this->assertTrue($this->stmt->isError());

		$error = $this->stmt->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);

		$this->assertEquals(10000, $error->getCode());
		$this->assertNull($error->getSqlState());
		$this->assertEquals(
			'bindParams fail: invalid args passed in',
			$error->getMessage()
		);
		$this->assertFalse($this->stmt->isParamsBound());

		
		$badParams = array('a' => 's', 1 => 1);
		$this->assertFalse($this->stmt->bindParams($badParams));	
		$this->assertTrue($this->stmt->isError());
		$this->assertEquals($error, $this->stmt->getError());
		$this->assertFalse($this->stmt->isParamsBound());

		/* this is a weird case that we don't check the numneric keys
		 * of the params to be bounded
		 */
		$badParams = array(0 => 's', 'a' => 1);
		$this->assertTrue($this->stmt->bindParams($badParams));	
		$this->assertTrue($this->stmt->isParamsBound());
		
		$this->assertTrue($this->stmt->close());
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testBindParamsFirstParamNoString($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		
		$badParams = array(123, 1);
		$this->assertFalse($this->stmt->bindParams($badParams));	
		$this->assertTrue($this->stmt->isError());

		$error = $this->stmt->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\DbError',
			$error
		);

		$this->assertEquals(10000, $error->getCode());
		$this->assertNull($error->getSqlState());
		$this->assertEquals(
			'bindParams fail: invalid args passed in',
			$error->getMessage()
		);
		$this->assertFalse($this->stmt->isParamsBound());

		
		$badParams = array(array(1234), 1);
		$this->assertFalse($this->stmt->bindParams($badParams));	
		$this->assertTrue($this->stmt->isError());
		$this->assertFalse($this->stmt->isParamsBound());
		$this->assertEquals($error, $this->stmt->getError());

		$badParams = array(new StdClass(), 'a' => 1);
		$this->assertFalse($this->stmt->bindParams($badParams));	
		$this->assertFalse($this->stmt->isParamsBound());
		$this->assertEquals($error, $this->stmt->getError());
		
		$this->assertTrue($this->stmt->close());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testBindParamsAfterClose($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());
		$this->assertTrue($this->stmt->close());
		
		$params = $this->stmt->normalizeParams($values);
		$this->stmt->bindParams($params);
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testExecute($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());
		$params = $this->stmt->normalizeParams($values);
		$this->assertTrue($this->stmt->bindParams($params));
		$this->assertTrue($this->stmt->isParamsBound());

		$this->assertTrue($this->stmt->execute());
		$this->assertTrue($this->stmt->isExecuted());
		$this->assertTrue($this->stmt->close());
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testExecuteBeforePrepare($sql, $values)
	{
		$this->assertFalse($this->stmt->isPrepared());
		$this->assertFalse($this->stmt->execute());
		$this->assertFalse($this->stmt->isExecuted());
		$this->assertTrue($this->stmt->isError());
		
		$error = $this->stmt->getError();
		$this->assertEquals(10001, $error->getCode());
		$this->assertEquals(
			'can not execute before prepare', 
			$error->getMessage()
		);
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testExecuteBeforeBoundParams($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());
		$this->assertFalse($this->stmt->isParamsBound());
		
		$this->assertFalse($this->stmt->execute());
		$error = $this->stmt->getError();

		$this->assertEquals(2031, $error->getCode());
		$this->assertEquals(
			'No data supplied for parameters in prepared statement', 
			$error->getMessage()
		);
		$this->assertEquals('HY000', $error->getSqlState());
		$this->assertTrue($this->stmt->close());
	}

	/**
	 * @dataProvider	provideInvalidSql 
	 * @return	null
	 */
	public function testExecuteWithFailedPrepare($sql, $values)
	{
		$this->assertFalse($this->stmt->prepare($sql));
		$this->assertFalse($this->stmt->isPrepared());

		$params = $this->stmt->normalizeParams($values);
		$this->assertFalse($this->stmt->bindParams($params));
		$this->assertFalse($this->stmt->isParamsBound());
		
		$this->assertFalse($this->stmt->execute());
		$error = $this->stmt->getError();
		
		$this->assertEquals(10001, $error->getCode());
		$this->assertEquals(
			'can not execute before prepare', 
			$error->getMessage()
		);
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return null
	 */
	public function testOrganizeResults($sql, $values)
	{
		$this->assertFalse($this->stmt->isResultset());
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());

		$params = $this->stmt->normalizeParams($values);

		$this->assertTrue($this->stmt->bindParams($params));
		$this->assertTrue($this->stmt->isParamsBound());
		
		$this->assertTrue($this->stmt->execute());
		$this->assertTrue($this->stmt->isExecuted());

		$this->assertTrue($this->stmt->organizeResults());
		$this->assertTrue($this->stmt->isResultset());
	}

	/**
	 * @dataProvider	provideInvalidSql 
	 * @return	null
	 */
	public function testOrganizeResultsWithFailedPrepare($sql, $values)
	{
		$this->assertFalse($this->stmt->prepare($sql));
		$this->assertFalse($this->stmt->isPrepared());

		$params = $this->stmt->normalizeParams($values);
		
		$this->assertFalse($this->stmt->organizeResults());
		$error = $this->stmt->getError();
		
		$this->assertEquals(10001, $error->getCode());
		$this->assertEquals(
			'can not organize results before prepare', 
			$error->getMessage()
		);
	}

	/**
	 * You can organize resultset before bound parameters and berfore exexuting
	 *
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testOrganizeResultsPreparedNotBounded($sql, $values)
	{
		$this->assertTrue($this->stmt->prepare($sql));
		$this->assertTrue($this->stmt->isPrepared());

		$this->assertTrue($this->stmt->organizeResults());
		$this->assertTrue($this->stmt->isResultset());
	}

	/**
	 * @dataProvider	provideBasicSql 
	 * @return	null
	 */
	public function testFetchBufferedResults($sql, $values)
	{
		$this->stmt->prepare($sql);
		$this->stmt->isPrepared();
		$params = $this->stmt->normalizeParams($values);
		$this->stmt->bindParams($params);
		$this->stmt->execute();
		$this->stmt->organizeResults();
		
		$ok = $this->stmt->storeResults();
		$results = $this->stmt->fetch();
		$this->stmt->freeStoredResults();
		
		$this->assertInternalType('array', $results);

		$expected = array(
			array(
				'query_id' => 1,
				'param_1'  => 1,
				'param_2'  => 'code_a',
				'param_3'  => 0,
				'result'   => 'query issued'
			),
		);
		$this->assertEquals($expected, $results);
	}
}
