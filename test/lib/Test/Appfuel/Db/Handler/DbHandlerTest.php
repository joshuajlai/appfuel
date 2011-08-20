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
namespace Test\Appfuel\Db\Adapter;

use Test\DbCase as ParentTestCase,
	Appfuel\Db\Request\QueryRequest,
	Appfuel\Db\Request\MultiQueryRequest,
	Appfuel\Db\Request\PreparedRequest,
	Appfuel\Db\Handler\DbHandler;

/**
 * Database handler has a static member called a pool. Not a real connection
 * pool as php is unable to do real connection pooling, it holds all the 
 * connection objects and hides the need to know about master and slave for
 * replication systems. This makes the dbhandle unware of replication so 
 * it only needs to service the request. Please that a seperate object is 
 * is to initialize connections into the handlers pool and we will be testing
 * the handlers ability get and set a pool as well as execute several types
 * of requests.
 */
class DbHandlerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var DbHandler
	 */
	protected $handler = null;

	/**
	 * Back up the connection pool because its static and we don't want to 
	 * effect current database operations
	 * @var Pool
	 */
	protected $bkPool = null;

	/**
	 * Save the current state of the Pool
	 */
	public function setUp()
	{
		$this->bkPool = DbHandler::getPool();
		$this->handler = new DbHandler();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		DbHandler::setPool($this->bkPool);
		unset($this->handler);
	}

	/**
	 * @return null
	 */
	public function provideConnectionTypes()
	{
		return array(
			array('read'),
			array('write'),
			array('both')
		);
	}

	/**
	 * @return null
	 */
	public function testGetSetPool()
	{
		$pool = $this->getMock('Appfuel\Framework\Db\Handler\PoolInterface');
		$this->assertNull(DbHandler::setPool($pool));
		$this->assertTrue(DbHandler::isPool());
		$this->assertSame($pool, DbHandler::getPool());
	}

	/**
	 * Using a QueryRequest set to read|write|both (should do slave connection)
	 * but we don't really care as it is handled by the pool which connection 
	 * is given as long as its a ConnectionInterface we run it
	 * 
	 * @dataProvider	provideConnectionTypes
	 * @return null
	 */
	public function testExecuteWithQueryRequest($type)
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		$request = new QueryRequest($type, $sql);
		
		$response = $this->handler->execute($request);
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\DbResponseInterface',
			$response
		);
		$this->assertTrue($response->isSuccess());

		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);
		$this->assertEquals($expected, $response->getCurrentResult());
	}

	/**
	 * 
	 * @dataProvider	provideConnectionTypes
	 * @return null
	 */
	public function testExecuteWithMultiQueryRequest($type)
	{
		$sql = array(
			'SELECT query_id, result FROM test_queries WHERE query_id=1',
			'SELECT query_id, result FROM test_queries WHERE query_id=3',
		);
		$request = new MultiQueryRequest($type, $sql);
		
		$response = $this->handler->execute($request);
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\DbResponseInterface',
			$response
		);
		$this->assertTrue($response->isSuccess());
	}
}
