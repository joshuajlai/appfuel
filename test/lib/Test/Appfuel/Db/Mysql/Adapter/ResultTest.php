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
	Appfuel\Db\Mysql\Adapter\Result,
	StdClass,
	mysqli,
	mysqli_result;

/**
 * 
 */
class ResultTest extends ParentTestCase
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
	public function handleProviderQueryId_1()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id=1';
		return array(
			array($sql)
		);	
	}

	/**
	 * @return array
	 */
	public function handleProviderQueryId_lt_4()
	{
		$sql = 'SELECT query_id, result FROM test_queries WHERE query_id < 4';
		return array(
			array($sql)
		);	
	}

	public function getHandle($sql)
	{
		$handle	= $this->server->getHandle();
		return $handle->query($sql);
	}


	/**
	 * The handle is made immutable by passing it through the constructor
	 * and having no public setter. IsHandle exists because after you 
	 * free the result set the result object istelf is of no use.
	 * like this.
	 *
	 * @dataProvider	handleProviderQueryId_1
	 * @return	null
	 */
	public function testGetHandleIsFreeIsValidType($sql)
	{
		$handle = $this->getHandle($sql);
		$result = new Result($handle);
		$this->assertSame($handle, $result->getHandle());
		$this->assertTrue($result->isHandle());

		$this->assertTrue($result->isValidType(MYSQLI_ASSOC));
		$this->assertTrue($result->isValidType(MYSQLI_NUM));
		$this->assertTrue($result->isValidType(MYSQLI_BOTH));

		/* does not exist */
		$this->assertFalse($result->isValidType(999));
	
		/* free the handle releasing the result set. */
		$this->assertNull($result->free());
		$this->assertFalse($result->isHandle());
		$this->assertNull($result->getHandle());
		$result->free();
	}

	/**
	 * The table used as the following structure:
	 *	query_id	int not null 
	 *	result		varchar(128) not null
	 *
	 * We will pull down a total of three rows to test fetchRow
	 * getFieldCount and getRowCount
	 *
	 * @dataProvider	handleProviderQueryId_lt_4
	 * @return null
	 */
	public function testFieldFetchRowCount($sql)
	{
		$result = new Result($this->getHandle($sql));
	
		/* test field count we know only 2 columns exists for this table*/
		$this->assertEquals(2, $result->getFieldCount());

		/* expecting only three results */
		$this->assertEquals(3, $result->getRowCount());
		
		$data = $result->fetchRow();
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);

		/* 
		 * test the data in the row is correct. 
		 */
		$this->assertEquals(1, $data[0]);
		$this->assertEquals('query issued', $data[1]);
	
		/* lets fetch the next row */
		$data = $result->fetchRow();
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);

		/* 
		 * test the data in the row is correct. 
		 */
		$this->assertEquals(2, $data[0]);
		$this->assertEquals('query 2 issued', $data[1]);
	
		/* lets fetch the last row */
		$data = $result->fetchRow();
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);

		/* 
		 * test the data in the row is correct. 
		 */
		$this->assertEquals(3, $data[0]);
		$this->assertEquals('query 3 issued', $data[1]);

		/* lets fetch beyond the last row */	
		$data = $result->fetchRow();
		$this->assertNull($data);
		$result->free();
	}

	/**
	 * Test fechAllRows processes the correct data
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchAllRows($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			array(1, 'query issued'),
			array(2, 'query 2 issued'),
			array(3, 'query 3 issued')
		);
		$this->assertEquals($expected, $result->fetchAllRows());
		$result->free();
	}

	/**
	 * SeekRow will advance the row point to the index given. We will test it
	 * by seeking to a given index and then calling fetchRow to assert the
	 * the correct row was given
	 *
	 * @dataProvider	handleProviderQueryId_lt_4
	 * @return null
	 */
	public function testSeekRow($sql)
	{
		$result = new Result($this->getHandle($sql));
	
		/* expecting only three results */
		$this->assertEquals(3, $result->getRowCount());
			
		/* prove this is the first row */	
		$data = $result->fetchRow();
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);
		$this->assertEquals(1, $data[0]);
		$this->assertEquals('query issued', $data[1]);
	
		/* seek to the last index */
		$this->assertTrue($result->seekRow(2));
		$data = $result->fetchRow();
	
		/* 
		 * prove this is the last row
		 */
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);
		$this->assertEquals(3, $data[0]);
		$this->assertEquals('query 3 issued', $data[1]);

		/* seek to the middle position */
		$this->assertTrue($result->seekRow(1));
		$data = $result->fetchRow();
	
		/* prove this is the middle row */
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);
		$this->assertEquals(2, $data[0]);
		$this->assertEquals('query 2 issued', $data[1]);

		/* seek to the first index */
		$this->assertTrue($result->seekRow(0));
		$data = $result->fetchRow();
	
		/* prove this is the first row */	
		$data = $result->fetchRow();
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);

		/* seek to a bad index */
		$this->assertFalse($result->seekRow(4));
		$this->assertFalse($result->seekRow(-1));
		$this->assertFalse($result->seekRow('abc'));
		$this->assertFalse($result->seekRow(array()));
		$this->assertFalse($result->seekRow(new StdClass()));

		/* will convert string to the correct number */
		$this->assertTrue($result->seekRow('1'));
		$data = $result->fetchRow();
	
		/* prove this is the middle row */
		$this->assertInternalType('array', $data);
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey(0, $data);
		$this->assertArrayHasKey(1, $data);
		$this->assertEquals(2, $data[0]);
		$this->assertEquals('query 2 issued', $data[1]);

		$result->free();	
	}

	/**
	 * The default behavior uses MYQLI_ASSOC which returns an associative
	 * array of column_name => column_value
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchAllDefault($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			array(
				'query_id' => 1,
				'result'   => 'query issued'
			),
			array(
				'query_id' => 2,
				'result'   => 'query 2 issued'
			),
			array(
				'query_id' => 3,
				'result'   => 'query 3 issued'
			),
		);

		$this->assertEquals($expected, $result->fetchAll());

		/* rewind back pointer to fetch the same row */
		$result->seekRow(0);
		$this->assertEquals($expected, $result->fetchAll(MYSQLI_ASSOC));
		
		$result->free();
	}

	/**
     * @dataProvider    handleProviderQueryId_lt_4
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
	 */
	public function testFetchAllBadArgument($sql)
	{
		$result = new Result($this->getHandle($sql));
		$this->assertFalse($result->fetchAll(-1));
	}

	/**
     * @dataProvider    handleProviderQueryId_lt_4
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
	 */
	public function testFetchAllBadArgumentString($sql)
	{
		$result = new Result($this->getHandle($sql));
		$this->assertFalse($result->fetchAll('abc'));
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchAllNum($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			array(1, 'query issued'),
			array(2, 'query 2 issued'),
			array(3, 'query 3 issued')
		);
		$this->assertEquals($expected, $result->fetchAll(MYSQLI_NUM));
		$result->free();
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchAllBoth($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			array(
				0		   => 1,
				'query_id' => 1,
				1		   => 'query issued',
				'result'   => 'query issued'
			),
			array(
				0			=> 2,
				'query_id'	=> 2,
				1			=> 'query 2 issued',
				'result'	=> 'query 2 issued'
			),
			array(
				0			=> 3,
				'query_id'	=> 3,
				1			=> 'query 3 issued',
				'result'	=> 'query 3 issued'
			),
		);
		$this->assertEquals($expected, $result->fetchAll(MYSQLI_BOTH));
		$result->free();
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchArrayDefault($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);

		$this->assertEquals($expected, $result->fetchArray());
		
		/* rewind the pointer to fetch back that same row */
		$result->seekRow(0);
		$this->assertEquals($expected, $result->fetchArray(MYSQLI_ASSOC));
		$result->free();
	}

	/**
     * @dataProvider    handleProviderQueryId_lt_4
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
	 */
	public function testFetchArrayBadArgument($sql)
	{
		$result = new Result($this->getHandle($sql));
		$this->assertFalse($result->fetchArray(-1));
	}

	/**
     * @dataProvider    handleProviderQueryId_lt_4
	 * @expectedException	Appfuel\Framework\Exception
     * @return null
	 */
	public function testFetchArrayBadArgumentString($sql)
	{
		$result = new Result($this->getHandle($sql));
		$this->assertFalse($result->fetchArray('abc'));
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchArrayNum($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			0	=> 1,
			1   => 'query issued'
		);

		$this->assertEquals($expected, $result->fetchArray(MYSQLI_NUM));
		$result->free();
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchArrayBoth($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			0			=> 1,
			'query_id'	=> 1,
			1			=> 'query issued',
			'result'	=> 'query issued'
		);

		$this->assertEquals($expected, $result->fetchArray(MYSQLI_BOTH));
		$result->free();
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchAssociativeArray($sql)
	{
		$result = new Result($this->getHandle($sql));
		$expected = array(
			'query_id' => 1,
			'result'   => 'query issued'
		);

		$this->assertEquals($expected, $result->fetchAssociativeArray());
		$result->free();
	}

	/**
	 *
     * @dataProvider    handleProviderQueryId_lt_4
     * @return null
	 */
	public function testFetchObject($sql)
	{
		$result = new Result($this->getHandle($sql));

		$expected = new StdClass();
		$expected->query_id = 1;
		$expected->result   = 'query issued';

		$this->assertEquals($expected, $result->fetchObject());
		$result->free();
	}

	/**
     * @dataProvider    handleProviderQueryId_1
     * @return  null
     */
    public function testGetFieldCountGetCurrentField($sql)
    {
		$result = new Result($this->getHandle($sql));
	
		/* we know this query is on a table with only two columns */
		$this->assertEquals(2, $result->getFieldCount());

		$field = $result->getField();
		$this->assertInstanceOf('StdClass', $field);
		$this->assertTrue(property_exists($field, 'name'));
		$this->assertTrue(property_exists($field, 'orgname'));
		$this->assertTrue(property_exists($field, 'table'));
		$this->assertTrue(property_exists($field, 'orgtable'));
		$this->assertTrue(property_exists($field, 'max_length'));
		$this->assertTrue(property_exists($field, 'length'));
		$this->assertTrue(property_exists($field, 'charsetnr'));
		$this->assertTrue(property_exists($field, 'flags'));
		$this->assertTrue(property_exists($field, 'type'));
		$this->assertTrue(property_exists($field, 'decimals'));

		$this->assertEquals('query_id',	$field->name);
		$this->assertEquals('query_id',	$field->orgname);
		$this->assertEquals('test_queries',	$field->table);
		$this->assertEquals('test_queries',	$field->orgtable);
		$this->assertEquals(1,	$field->max_length);

		$fields = $result->getFields();
		$this->assertInternalType('array', $fields);
		$this->assertEquals(2, count($fields));
		foreach ($fields as $field) {
			$this->assertInstanceOf('StdClass', $field);
			$this->assertTrue(property_exists($field, 'name'));
			$this->assertTrue(property_exists($field, 'orgname'));
			$this->assertTrue(property_exists($field, 'table'));
			$this->assertTrue(property_exists($field, 'orgtable'));
			$this->assertTrue(property_exists($field, 'max_length'));
			$this->assertTrue(property_exists($field, 'length'));
			$this->assertTrue(property_exists($field, 'charsetnr'));
			$this->assertTrue(property_exists($field, 'flags'));
			$this->assertTrue(property_exists($field, 'type'));
			$this->assertTrue(property_exists($field, 'decimals'));
		}
		$result->free();
	}

	/**
     * @dataProvider    handleProviderQueryId_1
     * @return  null
     */
    public function testGetCurrentFieldNumberSeek($sql)
    {
		$result = new Result($this->getHandle($sql));
		$this->assertEquals(0, $result->getCurrentFieldNumber());
		$this->assertEquals(2, $result->getFieldCount());
		
		/* field and metadata are the same thing */
		$metadata = $result->getFieldMetadata(0);
		$field = $result->getField();
		$this->assertEquals($field, $metadata);

		$this->assertTrue($result->fieldSeek(1));
		$field = $result->getField();
		$this->assertInstanceOf('StdClass', $field);
		$this->assertEquals('result', $field->name);

		$this->assertTrue($result->fieldSeek(0));
		$field = $result->getField();
		$this->assertInstanceOf('StdClass', $field);
		$this->assertEquals('query_id', $field->name);
	
		$result->free();
	}

	/**
     * @dataProvider    handleProviderQueryId_1
     * @return  null
     */
    public function testGetColumnLengths($sql)
    {
		$result = new Result($this->getHandle($sql));
		$row = $result->fetchRow();

		$lengths = $result->getColumnLengths();
		$this->assertInternalType('array', $lengths);
		$this->assertEquals(2, count($lengths));

		$this->assertEquals(strlen($row[0]), $lengths[0]);
		$this->assertEquals(strlen($row[1]), $lengths[1]);
		$result->free();
	}
}
