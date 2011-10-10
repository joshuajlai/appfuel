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
namespace TestFuel\Test\Db\Mysql\Constraint;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Mysql\Constraint\Key;

/**
 * Testing the mysql key constraint
 */
class KeyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DefaultValue
	 */
	protected $constraint = null;

	/**
	 * Column name of list of column names
	 * @var string
	 */
	protected $columnName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->indexName  = 'my-index-name';
		$this->columnName = 'my-column';
		$this->constraint = new Key($this->indexName, $this->columnName);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->constraint = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Constraint\ConstraintInterface',
			$this->constraint
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Constraint\ConstraintKeyInterface',
			$this->constraint
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSqlPhrase()
	{
		$this->assertEquals('key', $this->constraint->getSqlPhrase());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetIndexName()
	{
		$this->assertEquals(
			$this->indexName,
			$this->constraint->getIndexName()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetColumns()
	{
		$expected = array($this->columnName);
		$this->assertEquals($expected, $this->constraint->getColumns());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildSqlSingleColumnWithIndexName()
	{
		$expected = "key {$this->indexName} ({$this->columnName})";
		$this->assertEquals($expected, $this->constraint->buildSql());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testKeyNoIndexSingleColumn()
	{
		$column = 'my-column';
		$key = new key(null, $column);
		$this->assertNull($key->getIndexName());
		
		$expected = "key ($column)";
		$this->assertEquals($expected, $key->buildSql());

		$key = new key('', $column);
		$this->assertNull($key->getIndexName());
		$this->assertEquals($expected, $key->buildSql());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testKeyNoIndexManyColumns()
	{
		$column = array('col1', 'col2');
		$key = new key(null, $column);
		$this->assertNull($key->getIndexName());
		
		$expected = "key (col1,col2)";
		$this->assertEquals($expected, $key->buildSql());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testKeyIndexManyColumns()
	{
		$column = array('col1', 'col2');
		$key = new key('my_index', $column);
		$this->assertEquals('my_index', $key->getIndexName());
		
		$expected = "key my_index (col1,col2)";
		$this->assertEquals($expected, $key->buildSql());
	}



	
}

