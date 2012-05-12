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
namespace TestFuel\Test\Db\Mysql\DataType;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Mysql\DataType\Float;

/**
 * The Bit Datatype
 */
class FloatTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AbstractType
	 */
	protected $type = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->type = new Float();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->type = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Mysql\DataType\NumberTypeInterface',
			$this->type
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSqlString()
	{
		$this->assertEquals('float', $this->type->getSqlString());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetValidator()
	{
		$validator = 'datatype-float';
		$this->assertEquals($validator, $this->type->getValidatorName());
	}

	/**
	 * These are the values set when no parameters are given into the 
	 * contructor
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultZeroFillDisplayWidthAutoIncrement()
	{
		$this->assertNull($this->type->getDisplayWidth());
		$this->assertFalse($this->type->isZeroFill());
		$this->assertFalse($this->type->isUnsigned());
		$this->assertFalse($this->type->isAutoIncrement());
	}

	/**
	 * @return null
	 */
	public function xtestBuildSqlNoAttributes()
	{
		$expected = 'float';
		$this->assertEquals($expected, $this->type->buildSql());

	}

	/**
	 * @return	null
	 */	
	public function xtestBuildSqlDisplayWidth()
	{
		$expected = 'float(7)';
		$type = new Float('7');
		$this->assertEquals($expected, $type->buildSql());
	}

	/**
	 * @return	null
	 */
	public function xtestBuildSqlDisplayWidthPrecision()
	{
		$expected = 'float(7,4)';
		$type = new Float('7,4');
		$this->assertEquals($expected, $type->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testBuildSqlDisplayWidthPrecisionUnsigned()
	{
		$expected = 'float(7,4) unsigned';
		$type = new Float('7,4 unsigned');
		$this->assertEquals($expected, $type->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testBuildSqlWidthUnsignedAutoIncrement()
	{
		$expected = 'float(7,4) unsigned auto_increment';
		$type = new Float('7,4 unsigned auto_increment');
		$this->assertEquals($expected, $type->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testBuildSqlAllAttributes()
	{
		$expected = 'float(7,4) unsigned zerofill auto_increment';
		$type = new Float('7,4 unsigned auto_increment zerofill');
		$this->assertEquals($expected, $type->buildSql());

		/* not the order of the attribute declaration in the string
		 * does not matter, its always the same order in the resulting sql
		 */
		$type = new Float('7,4 auto_increment zerofill unsigned');
		$this->assertEquals($expected, $type->buildSql());

		$type = new Float('zerofill auto_increment 7,4 unsigned');
		$this->assertEquals($expected, $type->buildSql());
	}
}
