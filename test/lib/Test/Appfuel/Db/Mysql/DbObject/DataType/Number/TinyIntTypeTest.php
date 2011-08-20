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
	Appfuel\Db\Mysql\DbObject\DataType\Number\TinyInt;

/**
 * Mysql tiny int test
 */
class TinyIntTypeTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var AbstractIntType
	 */
	protected $type = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->type = new TinyInt();
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
		$this->assertEquals(255, $this->type->getUmax());
	}

	/**
	 * @return	null
	 */
	public function testSqlName()
	{
		$this->assertEquals('tinyint', $this->type->getSqlName());
	}

	/**
	 * @return	null
	 */
	public function testMaxMin()
	{
		$this->assertEquals(-128, $this->type->getMin());
		$this->assertEquals(127, $this->type->getMax());
	}
}
