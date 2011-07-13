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
    Appfuel\Db\Connection\ConnectionDetail,
    Appfuel\Db\Mysql\AfMysqli\Connection,
	Appfuel\Db\Mysql\AfMysqli\Query\Stmt;

/**
 * This class holds only the functionality to perform and debug queries.
 * The QueryTestCase holds all setUp and tearDown code aswell as 
 * data providers
 */
class StmtTest extends ParentTestCase
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
     * @return null
     */
    public function setUp()
    {
        $this->conn = new Connection($this->getConnDetail());
        $this->assertTrue($this->conn->initialize());
        $this->assertTrue($this->conn->connect());

        $this->query = new Stmt();
    }

    /**
     * @return null
     */
    public function tearDown()
    {  
        $this->assertTrue($this->conn->close());
        unset($this->conn);
        unset($this->query);
    }


	/**
	 * @return	null
	 */
	public function testBufferedSendQuery()
	{
		$driver = $this->conn->getDriver();
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($driver, $sql);

		$expected = array(
			array(
				'query_id' => 1,
				'result'   => 'query issued'
			)
		);
		$this->assertEquals($expected, $result);
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		
		/* this will not fail even though we did not free the result */
		$result = $this->query->execute($driver, $sql);
		$expected = array(
			array(
				'query_id' => 2,
				'result'   => 'query 2 issued'
			)
		);
		$this->assertEquals($expected, $result);
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=3';
		$result = $this->query->execute($driver, $sql);
		$expected = array(
			array(
				'query_id' => 3,
				'result'   => 'query 3 issued'
			)
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return	null
	 */
	public function testEmptySql()
	{
		$driver = $this->conn->getDriver();
		$error  = $this->query->execute($driver, '');
		$this->assertInstanceOf('Appfuel\Db\DbError', $error);

		$text = 'Invalid execute: sql is empty';
		$this->assertEquals('AF_ERR_MYSQLI_QUERY_STMT', $error->getCode());
		$this->assertEquals($text, $error->getMessage());
		$this->assertNull($error->getSqlState());
	}

	/**
	 * @return null
	 */
	public function testBadSqlCall()
	{
		$sql = 'SELECT query_id, result test_queries WHERE query_id=3';
		$driver = $this->conn->getDriver();
		$error  = $this->query->execute($driver, $sql);
		$this->assertFalse($error);
		$this->assertTrue($driver->errno > 0);
	}

	/**
	 * @return null
	 */
	public function testSelectInsertUpdateDelete()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=99';
		$driver = $this->conn->getDriver();
		$result  = $this->query->execute($driver, $sql);
		$this->assertEquals(array(), $result);
		
		$sqlInsert = 'INSERT INTO test_queries ' .
					 '(query_id, param_1, param_2, param_3, result) ' .
					 'VALUES (99, 1, "param_a", 1, "test query") ';

		$this->assertTrue($this->query->execute($driver, $sqlInsert));
	
		$result  = $this->query->execute($driver, $sql);
		$expected = array(
			array(
				'query_id' => 99,
				'result'   => 'test query'
			)
		);
		$this->assertEquals($expected, $result);

		$sqlUpdate = 'Update test_queries SET result="test query updated" ' .
					 'WHERE query_id=99';
	
		$this->assertTrue($this->query->execute($driver, $sqlUpdate));

		$result  = $this->query->execute($driver, $sql);
		$expected = array(
			array(
				'query_id' => 99,
				'result'   => 'test query updated'
			)
		);
		$this->assertEquals($expected, $result);

		$sqlDelete = 'DELETE FROM test_queries WHERE query_id=99';
		$this->assertTrue($this->query->execute($driver, $sqlDelete));
	
		$result  = $this->query->execute($driver, $sql);
		$this->assertEquals(array(), $result);
	}

	/**
	 * The query class always frees the result so you don't have to worry
	 * about use and store result flags from the prespective of having to
	 * free results
	 *
	 * @return	null
	 */
	public function testUnBufferedSendQuery()
	{
		$driver = $this->conn->getDriver();
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($driver, $sql, MYSQLI_USE_RESULT);
		$expected = array(
			array(
				'query_id' => 1,
				'result'   => 'query issued'
			)
		);
		$this->assertEquals($expected, $result);
		
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$result = $this->query->execute($driver, $sql);
		$expected = array(
			array(
				'query_id' => 2,
				'result'   => 'query 2 issued'
			)
		);
		$this->assertEquals($expected, $result);
	}
}
