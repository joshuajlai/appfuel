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
namespace TestFuel\Test\Db\Sql\Identifier;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Sql\Identifier\TableName;

/**
 * Test capabilities of the binary expression class
 */
class TableNameTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var TableName
	 */
	protected $tableName = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->tableName = new TableName('appfuel.users');
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
	public function testUnaryHasCorrectInterface()
	{	
		$this->assertInstanceOf(
			'Appfuel\Framework\Expr\ExprInterface',
			$this->tableName
		);
	}

	/**
	 * @return null
	 */
	public function testQualifiedName()
	{
		$this->assertTrue($this->tableName->isQualifiedName());
		$this->assertSame(
			$this->tableName, 
			$this->tableName->disableQualifiedName()
		);
		$this->assertFalse($this->tableName->isQualifiedName());
		$this->assertSame(
			$this->tableName, 
			$this->tableName->enableQualifiedName()
		);
		$this->assertTrue($this->tableName->isQualifiedName());
	}

	/**
	 * @return	null
	 */
	public function testConstructorQualifiedName()
	{
		$this->assertTrue($this->tableName->isQualifiedName());
		$this->assertEquals('users', $this->tableName->getOperand());
		$this->assertEquals('appfuel', $this->tableName->getParent());
		$this->assertEquals(
			'appfuel.users',
			$this->tableName->getQualifiedName()
		);

	}

	/**
	 * @return null
	 */
	public function xtestConstructorNoSchema()
	{
		$tableName = new TableName('users');
		$this->assertFalse($tableName->isSchema());
		$this->assertEquals('users', $tableName->getOperand());
		$this->assertNull($tableName->getSchema());
		$this->assertEquals(
			'users',
			$tableName->getQualifiedName()
		);

		/* will not enable when no schema exists */
		$this->assertSame($tableName, $tableName->enableSchema());
		$this->assertFalse($tableName->isSchema());
	}

	/**
	 * @return null
	 */
	public function xtestOutputWithSchema()
	{
		$this->expectOutputString('appfuel.users');
		echo $this->tableName;
	}

	/**
	 * @return null
	 */
	public function xtestOutputWithoutSchema()
	{
		$tableName = new TableName('users');
		$this->expectOutputString('users');
		echo $tableName;
	}

	/**
	 * @return null
	 */
	public function xtestOutputWithSchemaDisables()
	{
		$this->tableName->disableSchema();
		$this->expectOutputString('users');
		echo $this->tableName;
	}

	/**
	 * @return null
	 */
	public function xtestOutputWithoutSchemaSchemaEnabled()
	{
		$tableName = new TableName('users');
		$tableName->enableSchema();
		$this->expectOutputString('users');
		echo $tableName;
	}
}
