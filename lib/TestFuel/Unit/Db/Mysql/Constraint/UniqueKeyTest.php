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
	 * First parameter in the constructor
	 * @var string
	 */
	protected $indexName = null;

	/**
	 * Second paramter in the constructor
	 * @var string
	 */
	protected $columnName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->indexName  = 'my-index';
		$this->columnName = 'my-column';
		$this->constraint = new UniqueKey($this->indexName, $this->columnName);
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
		$this->assertEquals('unique key', $this->constraint->getSqlPhrase());
	}

	/**
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
	 * only a single column was added in the constructor during setup
	 * 
	 * @return	null
	 */
	public function getColumnsWhenSingleColumn()
	{
		$expected = array($this->columnName);
		$this->assertEquals($expected, $this->constraint->getColumns());
	}
}

