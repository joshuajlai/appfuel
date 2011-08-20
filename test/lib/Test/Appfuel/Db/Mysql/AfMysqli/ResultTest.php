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
namespace Test\Appfuel\Db\Mysql\AfMysqli;

use Test\DbCase as ParentTestCase,
	Appfuel\Db\Mysql\AfMysqli\Connection,
	Appfuel\Db\Mysql\AfMysqli\Result;

/**
 * Callback used in test below
 * 
 * @param	array	$row	raw record
 * @return	array
 */
function myResultMapper(array $row)
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
class ResultTest extends ParentTestCase
{
	protected $adapter = null;
	protected $driver = null;
	protected $conn = null;
	protected $resultHandle = null;
	protected $result = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->conn = new Connection($this->getConnDetail());
		$this->assertTrue($this->conn->initialize());
		$this->assertTrue($this->conn->connect());

		$driver = $this->conn->getDriver();
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=2';
	
		$this->resultHandle = $driver->query($sql);
		$this->assertInstanceOf('mysqli_result', $this->resultHandle);	
		$this->result = new Result($this->resultHandle);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->assertNull($this->result->free());
		$this->assertTrue($this->conn->close());
		unset($this->conn);
		unset($this->resultHandle);
		unset($this->result);
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

	public function testIsHandleFree()
	{
		$this->assertTrue($this->result->isHandle());
		$this->assertSame($this->resultHandle, $this->result->getHandle());
	}

	/**
	 * The handle is made immutable by passing it through the constructor
	 * and having no public setter. IsHandle exists because after you 
	 * free the result set the result object istelf is of no use.
	 * like this.
	 *
	 * @return	null
	 */
	public function testIsValidType()
	{
		$this->assertTrue($this->result->isValidType(MYSQLI_ASSOC));
		$this->assertTrue($this->result->isValidType(MYSQLI_NUM));
		$this->assertTrue($this->result->isValidType(MYSQLI_BOTH));

		/* does not exist */
		$this->assertFalse($this->result->isValidType(9990000222));
	}

	/**
	 * Ensure the result returns the correct column names for query in setup
	 * 
	 * @return null
	 */
	public function testGetColumnNames()
	{
		$names = $this->result->getColumnNames();
		$expected = array('query_id', 'result');
	}

	/**
	 * The default parameters are to return an associative and no callback
	 * is specified 
	 * 
	 */
	public function testFetchAllDataDefault()
	{
		$data = $this->result->fetchAllData();
		$expected = array(
			array(
				'query_id' => 2, 
				'result'   => 'query 2 issued'
			)
		);
		$this->assertEquals($expected, $data);
	}

    /**
     * @return null
     */
    public function testFetchAllDataWithClosureQuery()
    {
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

        $data = $this->result->fetchAllData(MYSQLI_ASSOC, $func);
        $expected = array(
			array(
				'myQueryId' => 2,
				'myResult'  => 'query 2 issued'
			)
        );
        $this->assertEquals($expected, $data);
    }

    /**
     * @return null
     */
    public function testFetchAllDataWithNumberClosureQuery()
    {
        $func = function (array $row) {
            $data = array();
            if (isset($row[0])) {
                $data['myQueryId'] = $row[0];
            }

            if (isset($row[1])) {
                $data['myResult'] = $row[1];
            }

            return $data;
        };

        $data = $this->result->fetchAllData(MYSQLI_NUM, $func);
        $expected = array(
            array('myQueryId' => 2,'myResult'  => 'query 2 issued')
        );
        $this->assertEquals($expected, $data);
    }

    /**
     * @return null
     */
    public function testFetchAllDataWithBothClosureQuery()
    {
        $func = function (array $row) {
            $data = array();
            if (isset($row[0])) {
                $data['myQueryId_index'] = $row[0];
            }

            if (isset($row['query_id'])) {
                $data['myQueryId_label'] = $row['query_id'];
            }


            if (isset($row[1])) {
                $data['myResult_index'] = $row[1];
            }

            if (isset($row['result'])) {
                $data['myResult_label'] = $row['result'];
            }


            return $data;
        };

        $data = $this->result->fetchAllData(MYSQLI_BOTH, $func);
        $expected = array(
			array(
				'myQueryId_index' => 2,
				'myQueryId_label' => 2,
				'myResult_index'  => 'query 2 issued',
				'myResult_label'  => 'query 2 issued'
			)
        );
        $this->assertEquals($expected, $data);
    }
}
