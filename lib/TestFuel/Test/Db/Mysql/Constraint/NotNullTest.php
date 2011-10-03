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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Mysql\Constraint\NotNull;

/**
 * This is the simplest of the constraints. We need to ensure that the sql is
 * correct thats it.
 */
class NotNullTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AbstractContraint
	 */
	protected $constraint = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->constraint = new NotNull();
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
	public function testGetSqlString()
	{
		$this->assertEquals('not null', $this->constraint->getSqlString());
	}

	/**
	 * @returns	null
	 */
	public function testBuildSqlDefault()
	{
		$this->assertEquals(
			'not null',
			$this->constraint->buildSql(),
			'sql is always defaulted to lowercase'
		);
	}

	/**
	 * @return	null
	 */
	public function testBuildSqlUppercase()
	{
		$this->constraint->enableUpperCase();
		$this->assertEquals('NOT NULL', $this->constraint->buildSql());
	}
}

