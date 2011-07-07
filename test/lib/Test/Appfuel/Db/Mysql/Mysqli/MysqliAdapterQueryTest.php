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
namespace Test\Appfuel\Db\Mysql\Mysqli\Adapter;

use Test\DbTestCase as ParentTestCase,
	Appfuel\Db\Mysql\Mysqli\Connection,
	Appfuel\Db\Mysql\Mysqli\Adapter;

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
class MysqliAdapterQueryTest extends ParentTestCase
{
	protected $adapter = null;
	protected $handle = null;
	protected $conn = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->handle  = mysqli_init();
        $this->conn = new Connection($this->getConnDetail(), $this->handle);
		$this->assertTrue($this->conn->connect());

		$this->adapter = new Adapter($this->handle);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertTrue($this->conn->close());
		unset($this->handle);
		unset($this->conn);
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
     * Callback used in test below
     * 
     * @param   array   $row    raw record
     * @return  array
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
	public function testCreateQuery()
	{
		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Mysqli\Query',
			$this->adapter->createQuery()
		);
	}

    /**
     * @return null
     */
    public function testExecuteSelectOneRowWithClosureQuery()
    {
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';

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
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());

        $expected = array('myQueryId' => 2,'myResult'  => 'query 2 issued');
        $this->assertEquals($expected, $response->getCurrentResult());
	}

    /**
     * @return null
     */
    public function testExecuteSelectOneRowCallbackQuery()
    {
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';

        $func =  array($this, 'myCustomMapper');
        $response = $this->adapter->executeQuery($sql, 'name', false, $func);

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());
        
		$expected = array('myQueryId' => 2,'myResult'  => 'query 2 issued');
        $this->assertEquals($expected, $response->getCurrentResult());
    }

    /**
     * @return null
     */
    public function testExecuteSelectOneRowCallbackNSQuery()
    {
        $sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';

        $func = __NAMESPACE__ . '\myMapper';
        $response = $this->adapter->executeQuery($sql, 'name', null, $func);

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());

        $expected = array('myQueryId' => 2,'myResult'  => 'query 2 issued');
        $this->assertEquals($expected, $response->getCurrentResult());
    }

    /**
     * @dataProvider    provideBasicSelectSql
     * @return null
     */
    public function testExecuteSelectOneRowDefault($sql)
    {
        $response = $this->adapter->executeQuery($sql);

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());

        $expected = array('query_id' => 2,'result'  => 'query 2 issued');
        $this->assertEquals($expected, $response->getCurrentResult());
    }

    /**
     * @dataProvider    provideBasicSelectSql
     * @return null
     */
    public function testExecuteSelectOneRowPosition($sql)
    {
        $response = $this->adapter->executeQuery($sql, 'position');

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());

        $expected = array(0 => 2, 1  => 'query 2 issued');
        $this->assertEquals($expected, $response->getCurrentResult());
    }


    /**
     * @dataProvider    provideBasicSelectSql
     * @return null
     */
    public function testExecuteSelectOneRowBoth($sql)
    {
        $response = $this->adapter->executeQuery($sql, 'both');

        $this->assertInstanceOf(
            'Appfuel\Db\DbResponse',
            $response
        );
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertNull($response->getError());
		$this->assertEquals(1, $response->getRowCount());
        $expected = array(
			0           => 2,
			'query_id'  => 2,
			1           => 'query 2 issued',
			'result'    => 'query 2 issued'
        );
        $this->assertEquals($expected, $response->getCurrentResult());
    }
}
