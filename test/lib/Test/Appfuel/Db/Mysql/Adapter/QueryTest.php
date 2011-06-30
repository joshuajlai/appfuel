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
namespace Test\Appfuel\Db\Mysql\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Adapter\Server,
	Appfuel\Db\Mysql\Adapter\Query,
	Mysqli;

/**
 * This class holds only the functionality to perform and debug queries
 */
class AdapterQueryTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Server
	 */
	protected $query = null;

	/**
	 * Hold the connection details
	 * @var ConnectionDetail
	 */
	protected $connDetail = null;
	
	/**
	 * Object responsible for opening and closing the connection
	 * @var Server
	 */
	protected $server = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->connDetail = new ConnectionDetail('mysql', 'mysqli');
		$this->connDetail->setHost('localhost')
						 ->setUserName('appfuel_user')
						 ->setPassword('w3b_g33k')
						 ->setDbName('af_unittest');

		$this->server = new Server($this->connDetail);
		$this->server->initialize();
		$this->server->connect();

		$this->query = new Query($this->server->getHandle());
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->server->close();
		unset($this->connDetail);
		unset($this->query);
		unset($this->server);
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
		$this->assertInstanceOf('Mysqli', $this->query->getHandle());
	}

	/**
	 * @dataProvider	sqlProviderQueryId_1
	 * @return	null
	 */
	public function testBufferedSendQuery($sql)
	{
		$resultClass = 'Appfuel\Db\Mysql\Adapter\Result';
		$result = $this->query->sendQuery($sql);
		$this->assertInstanceOf($resultClass, $result);

		$expected = array('query_id'=>1,'result' => 'query issued');
		$this->assertEquals($expected, $result->fetchArray());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		
		/* this will not fail even though we did not free the result */
		$result = $this->query->sendQuery($sql);
		$this->assertInstanceOf($resultClass, $result);

		$expected = array('query_id'=>2,'result' => 'query 2 issued');
		$this->assertEquals($expected, $result->fetchArray());
        
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=3';
		$result = $this->query->sendQuery($sql);
		$this->assertInstanceOf($resultClass, $result);

		$expected = array('query_id'=>3,'result' => 'query 3 issued');
		$this->assertEquals($expected, $result->fetchArray());
		$result->free();
	}

	/**
	 * @dataProvider	sqlProviderQueryId_1
	 * @return	null
	 */
	public function testUnBufferedSendQuery($sql)
	{
		$resultClass = 'Appfuel\Db\Mysql\Adapter\Result';
		$result = $this->query->sendQuery($sql, MYSQLI_USE_RESULT);
		$this->assertInstanceOf($resultClass, $result);
		
		$expected = array('query_id'=>1,'result' => 'query issued');
		$this->assertEquals($expected, $result->fetchArray());
		
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$this->assertFalse($this->query->sendQuery($sql));

		$handle = $this->query->getHandle();
		$this->assertEquals(2014, $handle->errno, 'mysql out of sync code');
		$this->assertEquals(
			"Commands out of sync; you can't run this command now",
			$handle->error,
			'mysql error messge'
		);

		$result->free();
		$result = $this->query->sendQuery($sql);
		$this->assertInstanceOf($resultClass, $result);

		$expected = array('query_id'=>2,'result' => 'query 2 issued');
		$this->assertEquals($expected, $result->fetchArray());
		$result->free();
	}

	/**
	 * @return null
	 */
	public function testMutlipleQueries()
	{
		$sql  = 'SELECT query_id, result FROM test_queries WHERE query_id=3;';
		$sql .= 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$result = $this->query->multipleQuery($sql);
		$expected = array(
			array(
				array(
					'query_id' => 3,
					'result'   => 'query 3 issued'
				)
			),
			array(
				array(
					'query_id' => 1,
					'result'   => 'query issued'
				)
			)
		);
		$this->assertEquals($expected, $result);
	}
}
