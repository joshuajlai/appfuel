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
namespace TestFuel\Test\Db\Schema;

use StdClass,
	Appfuel\Db\Schema\ForeignKey,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * The foreign key holds the columns from the table it belongs to aswell as the
 * reference table and columns that bind them togather. Like the other objects
 * this is an immutable so all setting is done via that constructor
 */
class ForeignKeyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ForeignKey
	 */
	protected $key = null;

	/**
	 * List of columns for primary key
	 * @var array
	 */
	protected $columns = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->columns    = array('colA', 'colB', 'colC');
		$this->refColumns = array('colX', 'colY', 'colZ');
		$this->refTable   = 'my-table';
		$this->key = new ForeignKey(
			$this->columns, 
			$this->refTable, 
			$this->refColumns
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->key = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\ForeignKeyInterface',
			$this->key,
			'must implment this interface'
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetReferenceTableName()
	{
		$this->assertEquals(
			$this->refTable, 
			$this->key->getReferenceTableName()
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsGetColumnNames()
	{
		$this->assertEquals($this->columns, $this->key->getColumnNames());

		foreach ($this->columns as $col) {
			$this->assertTrue($this->key->isKey($col));
		}
		$this->assertFalse($this->key->isKey('not-likely-a-column__Name'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsGetReferenceColumnNames()
	{
		$this->assertEquals(
			$this->refColumns, 
			$this->key->getReferenceColumnNames()
		);

		foreach ($this->refColumns as $col) {
			$this->assertTrue($this->key->isReferenceKey($col));
		}
		$this->assertFalse($this->key->isReferenceKey('not-a-columnName'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorWithSingleColumn()
	{
		$name = 'my-column-key';
		$tbl  = 'my-reference-table';
		$refcol = 'my-reference-column';
		$key = new ForeignKey($name, $tbl, $refcol);
		$this->assertEquals(array($name), $key->getColumnNames());
		$this->assertTrue($key->isKey($name));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyArrayColumns()
	{
		$key = new ForeignKey(array(), 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyStringTableName()
	{
		$key = new ForeignKey('col', '', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyStringReferenceColName()
	{
		$key = new ForeignKey('col', 'table', '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyColumnInListReferenceColumn()
	{
		$list = array('colA', '', 'colb');
		$key = new ForeignKey('col', 'tbl', $list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyStringColumns()
	{
		$key = new ForeignKey('', 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testObjectColumns()
	{
		$key = new ForeignKey(new StdClass(), 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyColumnInList()
	{
		$list = array('colA', '', 'colb');
		$key = new ForeignKey($list, 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testArrayColumnInList()
	{
		$list = array('colA', array(1,2,3), 'colb');
		$key = new ForeignKey($list, 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testObjectColumnInList()
	{
		$list = array(new StdClass(), 'colA','colb');
		$key = new ForeignKey($list, 'tbl', 'ref-col');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testIntColumnInList()
	{
		$list = array('colA','colb', 12345);
		$key = new ForeignKey($list, 'tbl', 'ref-col');
	}
}
