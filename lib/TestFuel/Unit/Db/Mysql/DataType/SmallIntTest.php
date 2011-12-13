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
	Appfuel\Db\Mysql\DataType\SmallInt;

/**
 * The SmallInt Datatype
 */
class SmallIntTest extends BaseTestCase
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
		$this->type = new SmallInt();
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
		$this->assertEquals('smallint', $this->type->getSqlString());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetValidator()
	{
		$validator = 'mysql-datatype-smallint';
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
}
