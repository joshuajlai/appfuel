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
namespace Test\Appfuel\Db\Sql\Identifier;

use StdClass,
	SplFileInfo,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Sql\Identifier\ColumnName;

/**
 *
 */
class ColumnNameTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var TableName
	 */
	protected $columnName = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->columnName = new ColumnName('my_table.users');
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		unset($this->tableName);
	}

	/**
	 * @return null
	 */
	public function testHasCorrectInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Framework\Expr\ExprInterface',
			$this->columnName
		);
	}

}
