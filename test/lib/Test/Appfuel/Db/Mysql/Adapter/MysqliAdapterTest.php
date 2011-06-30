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
	Appfuel\Db\Mysql\Adapter\MysqliAdapter;

/**
 * Callback used in test below
 * 
 * @param	array	$row	raw record
 * @return	array
 */
function myMapper(array $row)
{
	$data = array();
	if (isset($row['query_id'])) {
		$data['myQueryId'] = $row['query_id'];
	}

	if (isset($row['result'])) {
		$data['myResult'] = $row['result'];
	}

	return $data;
}


/**
 */
class MysqliAdapterTest extends ParentTestCase
{
	/**
	 * System under test
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

		$this->server  = new Server($this->connDetail);
		$this->adapter = new MysqliAdapter($this->server);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->server);
		unset($this->adapter);
	}

	/**
	 * @return array
	 */
	public function provideBasicSelectSql()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		return array(
			array($sql)
		);
	}

	/**
	 * Immutable object set in the constructor
	 *
	 * @return null
	 */
	public function testGetServer()
	{
		$this->assertSame($this->server, $this->adapter->getServer()); 
	}

	/**
	 * @return null
	 */
	public function testIsConnectedConnectClose()
	{
		$this->assertFalse($this->adapter->isError());
		$this->assertFalse($this->adapter->isConnected());
		$this->assertTrue($this->adapter->connect());
		$this->assertTrue($this->adapter->isConnected());
		$this->assertFalse($this->adapter->isError());
	
		/* will return true when already connected */
		$this->assertTrue($this->adapter->connect());	
		
		$this->assertTrue($this->adapter->close());
		$this->assertFalse($this->adapter->isConnected());
		$this->assertFalse($this->adapter->isError());
	}

	/**
	 * @return null
	 */
	public function testBadConnection()
	{
        $connDetail = new ConnectionDetail('mysql', 'mysqli');
        $connDetail->setHost('localhost')
                   ->setUserName('_not_likely_to_exist_apfuel__')
                   ->setPassword('no-pass')
                   ->setDbName('no-db');


        $server  = new Server($connDetail);
		$adapter = new MysqliAdapter($server);
		$this->assertFalse($adapter->isConnected());
		$this->assertFalse($adapter->connect());
		$this->assertTrue($adapter->isError());

		$error = $adapter->getError();
		$this->assertInstanceof(
			'Appfuel\Db\Mysql\Adapter\Error',
			$error
		);
		/* mysql access denied error code */
		$this->assertEquals(1045, $error->getCode());
	}

	/**
	 * @return null
	 */
	public function testExecuteSelectOneRowWithClosureQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$this->assertTrue($this->adapter->connect());

		$func = function (array $row) {
			$data = array();
			if (isset($row['query_id'])) {
				$data['myQueryId'] = $row['query_id'];
			}

			if (isset($row['result'])) {
				$data['myResult'] = $row['result'];
			}

			return $data;
		};

		$response = $this->adapter->executeQuery($sql, 'name', false, $func);	

		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());	
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array('myQueryId' => 2,'myResult'  => 'query 2 issued')
			)	
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}

	/**
	 * Callback used in test below
	 * 
	 * @param	array	$row	raw record
	 * @return	array
	 */
	public function myCustomMapper(array $row)
	{
		$data = array();
		if (isset($row['query_id'])) {
			$data['myQueryId'] = $row['query_id'];
		}

		if (isset($row['result'])) {
			$data['myResult'] = $row['result'];
		}

		return $data;
	}

	/**
	 * @return null
	 */
	public function ctestExecuteSelectOneRowCallbackQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$this->assertTrue($this->adapter->connect());

		$func =  array($this, 'myCustomMapper');
		$response = $this->adapter->executeQuery($sql, 'name', false, $func);

		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());	
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array('myQueryId' => 2,'myResult'  => 'query 2 issued')
			)	
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}

	/**
	 * @return null
	 */
	public function testExecuteSelectOneRowCallbackNSQuery()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
		$this->assertTrue($this->adapter->connect());

		$func = __NAMESPACE__ . '\myMapper'; 
		$response = $this->adapter->executeQuery($sql, 'name', null, $func);
		
		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());

		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array('myQueryId' => 2,'myResult'  => 'query 2 issued')
			)	
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}

	/**
	 * @dataProvider	provideBasicSelectSql
	 * @return null
	 */
	public function stestExecuteSelectOneRowDefault($sql)
	{
		$this->assertTrue($this->adapter->connect());

		$response = $this->adapter->executeQuery($sql);

		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());	
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array('query_id' => 2,'result'  => 'query 2 issued')
			)	
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}

	/**
	 * @dataProvider	provideBasicSelectSql
	 * @return null
	 */
	public function stestExecuteSelectOneRowPosition($sql)
	{
		$this->assertTrue($this->adapter->connect());

		$response = $this->adapter->executeQuery($sql, 'position');

		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());	
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array('myQueryId' => 2,'myResult'  => 'query 2 issued')
			)	
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}

	/**
	 * @dataProvider	provideBasicSelectSql
	 * @return null
	 */
	public function stestExecuteSelectOneRowBoth($sql)
	{
		$this->assertTrue($this->adapter->connect());

		$response = $this->adapter->executeQuery($sql, 'both');

		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\DbResponse',
			$response
		);
		$this->assertTrue($response->isSuccess());
		$this->assertFalse($response->isError());
		$this->assertNull($response->getError());	
		$expected = array(
			'row-count' => 1,
			'resultset' => array(
				array(
					0			=> 2,
					'query_id'	=> 2,
					1			=> 'query 2 issued',
					'result'	=> 'query 2 issued'
				)
			)
		);
		$this->assertEquals($expected, $response->getData());
		$this->adapter->close();
	}
}
