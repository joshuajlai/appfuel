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
namespace Test\Appfuel\Db\Mysql\DbObject\DataType;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Mysql\DbObject\DataType\Number\SmallInt;

/**
 * Mysql small int test
 */
class SmallIntTypeTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var SmallInt
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
		unset($this->type);
	}

	/**
	 * @return null
	 */
	public function testUminMmax()
	{
		/* unsigned min is always 0 */
		$this->assertEquals(0, $this->type->getUmin());
		$this->assertEquals(65535, $this->type->getUmax());
	}

	/**
	 * @return	null
	 */
	public function testSqlName()
	{
		$this->assertEquals('smallint', $this->type->getSqlName());
	}

	/**
	 * @return	null
	 */
	public function testMaxMin()
	{
		$this->assertEquals(-32768, $this->type->getMin());
		$this->assertEquals(32767, $this->type->getMax());
	}
}
