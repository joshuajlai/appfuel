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
namespace TestFuel\Test\Db\Mysql\AfMysqli\MultiQuery;

use mysqli,
    TestFuel\TestCase\DbTestCase,
    Appfuel\Db\Mysql\AfMysqli\Connection,
    Appfuel\Db\Connection\ConnectionDetail,
    Appfuel\Db\Mysql\AfMysqli\MultiQuery\Stmt,
    Appfuel\Db\Mysql\AfMysqli\MultiQuery\Adapter;


/**
 * This class holds only the functionality to perform and debug queries
 */
class StmtTest extends DbTestCase
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
	 * Mysqli driver used in tests
	 * @var mysqli
	 */
	protected $driver = null;

	/**
	 * Callback used to create a reponse object
	 * @var array
	 */	
	protected $responseCallback = array();

    /**
     * @return null
     */
    public function setUp()
    {  
        $this->conn = new Connection($this->getConnectionDetail());
        $this->assertTrue($this->conn->initialize());
        $this->assertTrue($this->conn->connect());

		$this->driver = $this->conn->getDriver();
		$adapter = new Adapter($this->driver);

		$this->responseCallback = array(
			$adapter,
			'createResponse'
		);

        $this->query = new Stmt();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $this->assertTrue($this->conn->close());
        unset($this->driver);
		unset($this->conn);
        unset($this->query);
		
    }

	/**
	 * @return null
	 */
	public function testExecuteValid()
	{
		$sql  = 'SELECT query_id, result FROM test_queries WHERE query_id=3;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->execute(
			$this->driver, 
			$sql,
			$this->responseCallback
		);
		$this->assertInternalType('array', $result);
		$this->assertEquals(2, count($result));
		$this->assertArrayHasKey(0, $result);
		$this->assertArrayHasKey(1, $result);
		
		$class = 'Appfuel\Db\DbResponse';
		$this->assertInstanceOf($class, $result[0]);
		$this->assertInstanceOf($class, $result[0]);

		$expected = array(
			'query_id' => 3,
			'result'   => 'query 3 issued'
		);
		$this->assertEquals(1, $result[0]->getRowCount());
		$this->assertEquals($expected, $result[0]->getCurrentResult());
	
		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);	
		$this->assertEquals($expected, $result[1]->getCurrentResult());
	}

	/**
	 * Multiqueries are usually returned as an array index numerically 
	 * increasing by one where each index represents the result from the 
	 * sql given. In this test we have three sql stmts separated by semicolons.
	 * The method executeMultiple takes an optional options arrray. With this
	 * we can provide a map to translate index numbers into names keys.
	 *
	 * @return null
	 */
	public function testMutlipleQueriesKeysResultKeys()
	{
		$sql  = 'SELECT query_id, result FROM test_queries WHERE query_id=3;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=1;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=2';

		$options = array(
			0 => array('result-key' => 'first-query'),
			1 => array('result-key' => 'second-query'),
			2 => array('result-key' => 'third-query')
		);

		$result = $this->query->execute(
			$this->driver, 
			$sql,
			$this->responseCallback, 
			$options
		);

		$this->assertInternalType('array', $result);
		$this->assertEquals(3, count($result));
		$this->assertArrayHasKey('first-query', $result);
		$this->assertArrayHasKey('second-query', $result);
		$this->assertArrayHasKey('third-query', $result);
	
		$class = 'Appfuel\Db\DbResponse';
		$firstRs  = $result['first-query'];
		$secondRs = $result['second-query'];
		$thirdRs  = $result['third-query'];

		$this->assertInstanceOf($class, $firstRs);
		$this->assertInstanceOf($class, $secondRs);
		$this->assertInstanceOf($class, $thirdRs);

		$expected = array(
			'query_id' => 3,
			'result'   => 'query 3 issued'
		);
	
		$this->assertEquals(1, $firstRs->getRowCount());
		$this->assertEquals($expected, $firstRs->getCurrentResult());
	
		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);
	
		$this->assertEquals(1, $secondRs->getRowCount());
		$this->assertEquals($expected, $secondRs->getCurrentResult());

		$expected = array(
			'query_id' => 2,
			'result'   => 'query 2 issued'
		);
	
		$this->assertEquals(1, $thirdRs->getRowCount());
		$this->assertEquals($expected, $thirdRs->getCurrentResult());
	
	}

	/**
	 * @return null
	 */
	public function testMutlipleQueriesKeysCallbacksNoResultKeys()
	{
		$firstMapper = function (array $row) {
			$data = array();
			if (isset($row['query_id'])) {
				$data['my-id'] = $row['query_id'];
			}

			if (isset($row['result'])) {
				$data['my-result'] = $row['result'];
			}

			return $data;
		};

		$secondMapper = function (array $row) {
			$data = array();
			if (isset($row['query_id'])) {
				$data['my-other-id'] = $row['query_id'];
			}

			if (isset($row['result'])) {
				$data['my-other-result'] = $row['result'];
			}

			return $data;
		};


		$sql  = 'SELECT query_id, result FROM test_queries WHERE query_id=3;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=1';

		$options = array(
			0 => array('callback' => $firstMapper),
			1 => array('callback' => $secondMapper),
		);

		$result = $this->query->execute(
			$this->driver, 
			$sql,
			$this->responseCallback, 
			$options
		);
		$this->assertInternalType('array', $result);
		$this->assertEquals(2, count($result));
		$this->assertArrayHasKey(0, $result);
		$this->assertArrayHasKey(1, $result);
	
		$class = 'Appfuel\Db\DbResponse';
		$firstRs  = $result[0];
		$secondRs = $result[1];

		$this->assertInstanceOf($class, $firstRs);
		$this->assertInstanceOf($class, $secondRs);

		$expected = array(
			'my-id'		=> 3,
			'my-result' => 'query 3 issued'
		);
	
		$this->assertEquals(1, $firstRs->getRowCount());
		$this->assertEquals($expected, $firstRs->getCurrentResult());

		$expected = array(
			'my-other-id'		=> 1,
			'my-other-result'	=> 'query issued'
		);
	
		$this->assertEquals(1, $secondRs->getRowCount());
		$this->assertEquals($expected, $secondRs->getCurrentResult());
	}

	/**
	 * @return null
	 */
	public function testMutlipleQueriesSqlErrorFirstQuery()
	{
		$sql  = 'SELECT query_id, result FROM test_queries WHERE uery_id=3;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=1;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=2;';

		$options = array(
			0 => array('result-key' => 'first-query'),
			1 => array('result-key' => 'second-query'),
			2 => array('result-key' => 'third-query'),
		);
		$result = $this->query->execute(
			$this->driver, 
			$sql,
			$this->responseCallback, 
			$options
		);
		
		$this->assertInstanceOf(
			'Appfuel\Db\DbResponse',
			$result
		);

		$this->assertTrue($result->isError());

		$error = $result->getError();
		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\AfMysqli\MultiQuery\Error',
			$error
		);

		/* indicates the error happened before the result loop */
		$this->assertEquals(-1, $error->getIndex());
	}
}
