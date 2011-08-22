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
namespace TestFuel\Test\Db\Mysql\DbObject\DataType;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Mysql\DbObject\DataType\Number\BigInt;

/**
 * Mysql big int test
 */
class BigIntTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Int
	 */
	protected $type = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->type = new BigInt();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->type);
	}

	/**
	 * @return null
	 */
	public function testUminMmax()
	{
		/* unsigned min is always 0 */
		$this->assertEquals(0, $this->type->getUmin());
		$this->assertEquals(18446744073709551615, $this->type->getUmax());
	}

	/**
	 * @return	null
	 */
	public function testSqlName()
	{
		$this->assertEquals('bigint', $this->type->getSqlName());
	}

	/**
	 * @return	null
	 */
	public function testMaxMin()
	{
		$this->assertEquals(-9223372036854775808, $this->type->getMin());
		$this->assertEquals(9223372036854775807, $this->type->getMax());
	}
}
