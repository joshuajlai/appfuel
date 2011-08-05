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
namespace Test\Appfuel\Db\Request;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Request\MultiQueryRequest;

/**
 */
class MultiQueryRequestTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var	QueryRequest
	 */
	protected $request = null;

	/**
	 * Type of db operation
	 * @var string
	 */
	protected $opType = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->opType  = 'read';
		$this->request = new MultiQueryRequest($this->opType);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->request);
	}

	/**
	 * @return	array
	 */
	public function provideValidTypes()
	{
		return array(
			array('read'),
			array('write'),
			array('both')
		);
	}

	/**
	 * When sql has not be set addSql will use setSql instead of trying to 
	 * append.
	 *
	 * @return null
	 */
	public function testAddSqlWhenNoSqlExists()
	{
		/* default is no sql because no was passed into constructor for 
		 * setup
		 */
		$this->assertFalse($this->request->isSql());
		$sql = "SELECT * FROM TABLE WHERE id=blah";
		$this->assertSame(
			$this->request,
			$this->request->addSql($sql),
			'must use a fluent interface'
		);
		$this->assertTrue($this->request->isSql());
		$this->assertEquals($sql, $this->request->getSql());
	}

	/**
	 * When you use addSql multiple times, each string is concatenated and
	 * use setSql to assign back the result
	 *
	 * @depends		testAddSqlWhenNoSqlExists
	 * @return null
	 */
	public function testAddSqlMultipleTimes()
	{
		$sql = "SELECT * FROM TABLE WHERE id=blah";
		$this->request->setSql($sql);
		$this->assertTrue($this->request->isSql());
		$this->assertEquals($sql, $this->request->getSql());

		$sql2 = "SELECT * FROM TABLE WHERE id=foo";
		$this->request->addSql($sql2);
		$this->assertTrue($this->request->isSql(), 'should not change');

		$expected = "{$sql};$sql2";
		$this->assertEquals($expected, $this->request->getSql());
	
		$sql3 = "SELECT * FROM TABLE WHERE id=bar";
		$this->request->addSql($sql3);
		$this->assertTrue($this->request->isSql(), 'should not change');

		$expected = "{$sql};{$sql2};$sql3";
		$this->assertEquals($expected, $this->request->getSql());

		/* reset to one sql like this */
		$this->request->setSql($sql);
		$this->assertTrue($this->request->isSql(), 'should not change');
		$this->assertEquals($sql, $this->request->getSql());	
	}

	/**
	 * When you use addSql multiple times, each string is concatenated and
	 * use setSql to assign back the result
	 *
	 * @depends		testAddSqlMultipleTimes
	 * @return null
	 */
	public function testLoadSqlNoSqlExists()
	{
		$sql1 = "SELECT * FROM TABLE WHERE id=blah";
		$sql2 = "SELECT * FROM TABLE WHERE id=foo";
		$sql3 = "SELECT * FROM TABLE WHERE id=bar";
		$sql = array($sql1, $sql2, $sql3);

		$this->assertFalse($this->request->isSql());
		$this->assertSame(
			$this->request,
			$this->request->loadSql($sql),
			'must use a fluent interface'
		);
		$this->assertTrue($this->request->isSql());

		$expected = "{$sql1};{$sql2};$sql3";
		$this->assertEquals($expected, $this->request->getSql());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlAddSql_EmptyString()
	{
		$this->request->addSql('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlAddSql_Array()
	{
		$this->request->addSql(array('SELECT * FROM TABLE'));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlAddSql_Object()
	{
		$this->request->addSql(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlAddSql_Int()
	{
		$this->request->addSql(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlLoadSql_EmptyString()
	{
		$sql = array(
			'SELECT * FROM TABLE',
			''
		);
		$this->request->loadSql($sql);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlLoadSql_Array()
	{
		$sql = array(
			'SELECT * FROM TABLE',
			array('SELECT * FROM OTHER_TABLE')
		);
		$this->request->loadSql($sql);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testInvalidSqlLoadSql_Number()
	{
		$sql = array(
			'SELECT * FROM TABLE',
			12345
		);
		$this->request->loadSql($sql);
	}

	/**
	 * Make sure we can set the type of operation and sql
	 * 
	 * @dataProvider	provideValidTypes
	 * @return	null
	 */
	public function testConstructorTypeSqlNull($type)
	{
		$request = new MultiQueryRequest($type);
		$this->assertEquals($type, $request->getType());
		$this->assertFalse($request->isSql());
		$this->assertNull($request->getSql());
	}

	/**
	 * @return null
	 */
	public function testConstructorSqlString()
	{
		$sql = "Select * from table where id = blah";
		$request = new MultiQueryRequest('read', $sql);
		$this->assertTrue($request->isSql());
		$this->assertEquals($sql, $request->getSql());
	}

	/**
	 * @return null
	 */
	public function testConstructorSqlArray()
	{
		$sql1 = "Select * from table where id = blah";
		$sql2 = "Select * from other-table where id = blah";
		$sql = array($sql1,$sql2);
		$expected = "{$sql1};{$sql2}";

		$request = new MultiQueryRequest('read', $sql);
		$this->assertTrue($request->isSql());
		$this->assertEquals($expected, $request->getSql());
	}


	/**
	 * @return null
	 */
	public function testGetSetResultOptions()
	{
		$this->assertEquals(array(), $this->request->getResultOptions());
	
		$options = array(1,2,3);
		$this->assertSame(
			$this->request, 
			$this->request->setResultOptions($options)
		);

		$this->assertEquals($options, $this->request->getResultOptions());

		/* empty array is valid */
		$options = array();
		$this->assertSame(
			$this->request, 
			$this->request->setResultOptions($options)
		);

		$this->assertEquals($options, $this->request->getResultOptions());
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadNoParams()
	{
		$this->request->setResultOptions();
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadString()
	{
		$this->request->setResultOptions('this is a string');
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadInt()
	{
		$this->request->setResultOptions(12434);
	}

	/**
	 * @expectedException	Exception
	 * @return null
	 */
	public function testSetResultOptionsBadObject()
	{
		$this->request->setResultOptions(new StdClass());
	}
}
