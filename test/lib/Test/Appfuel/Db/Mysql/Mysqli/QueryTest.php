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
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Mysqli\Query,
	Appfuel\Db\Mysql\Mysqli\Connection,
	mysqli;

/**
 * This class holds only the functionality to perform and debug queries
 */
class QueryTest extends ParentTestCase
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

	/**
	 * The handle is made immutable by passing it through the constructor
	 * and having no public setter.
	 *
	 * @return	null
	 */
	public function testGetHandle()
	{
		$this->assertInstanceOf('mysqli', $this->query->getHandle());
	}

	/**
	 * @return	null
	 */
	public function testBufferedSendQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());

		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		
		/* this will not fail even though we did not free the result */
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());


		$expected = array(
			'query_id' => 2,
			'result'   => 'query 2 issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=3';
		$result = $this->query->execute($sql);
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);
		$this->assertEquals(1, $result->getRowCount());
		
		$expected = array(
			'query_id' => 3,
			'result'   => 'query 3 issued'
		);
		$this->assertEquals($expected, $result->getCurrentResult());
	}

	/**
	 * The query class always frees the result so you don't have to worry
	 * about use and store result flags from the prespective of having to
	 * free results
	 *
	 * @return	null
	 */
	public function xtestUnBufferedSendQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute($sql, MYSQLI_USE_RESULT);
		
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array(
					'query_id' => 1,
					'result'   => 'query issued'
				)
			)
		);
		$this->assertEquals($expected, $result);
		
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$result = $this->query->execute($sql);
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array(
					'query_id' => 2,
					'result'   => 'query 2 issued'
				)
			)
		);
		$this->assertEquals($expected, $result);
	}
}
