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
	Appfuel\Db\Mysql\Constraint\UniqueKey;

/**
 * Testing the constructor used to add columns and the buildSql which is 
 * the sql fragment in a create or alter table
 */
class UniqueKeyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DefaultValue
	 */
	protected $constraint = null;

	/**
	 * Only paramter in the constructor for the parent name
	 * @var string
	 */
	protected $columnName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->columnName = 'my-column';
		$this->constraint = new UniqueKey($this->columnName);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->constraint = null;
	}

	/**
	 * Value has no meaning in this constraint
	 * @return null
	 */
	public function testGetValue()
	{
		$this->assertNull($this->constraint->getValue());
	}

	/**
	 * @return	null
	 */
	public function testGetSqlString()
	{
		$this->assertEquals('unique key', $this->constraint->getSqlString());
	}
}

