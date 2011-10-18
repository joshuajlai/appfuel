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
	Appfuel\Db\Schema\PrimaryKey,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 */
class PrimaryKeyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var PrimaryKey
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
		$this->columns = array('colA', 'colB', 'colC');
		$this->key = new PrimaryKey($this->columns);
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
			'Appfuel\Framework\Db\Schema\PrimaryKeyInterface',
			$this->key,
			'must implment this interface'
		);
	}

	/**
	 * @dends	testInterface
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
	public function testConstructorWithSingleColumn()
	{
		$name = 'my-column-key';
		$key = new PrimaryKey($name);
		$this->assertEquals(array($name), $key->getColumnNames());
		$this->assertTrue($key->isKey($name));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyArrayColumns()
	{
		$key = new PrimaryKey(array());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyStringColumns()
	{
		$key = new PrimaryKey('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testObjectColumns()
	{
		$key = new PrimaryKey(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testEmptyColumnInList()
	{
		$list = array('colA', '', 'colb');
		$key = new PrimaryKey($list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testArrayColumnInList()
	{
		$list = array('colA', array(1,2,3), 'colb');
		$key = new PrimaryKey($list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testObjectColumnInList()
	{
		$list = array(new StdClass(), 'colA','colb');
		$key = new PrimaryKey($list);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testIntColumnInList()
	{
		$list = array('colA','colb', 12345);
		$key = new PrimaryKey($list);
	}




}
