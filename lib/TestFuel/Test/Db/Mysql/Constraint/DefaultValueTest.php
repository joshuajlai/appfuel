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
	Appfuel\Db\Mysql\Constraint\DefaultValue;

/**
 * The default value takes a single argument which is the value. This can
 * be any scalar value or an object representing to string
 */
class DefaultValueTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DefaultValue
	 */
	protected $constraint = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->constraint = new DefaultValue('my-value');
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->constraint = null;
	}

	/**
	 * @return null
	 */
	public function testGetValue()
	{
		$this->assertEquals('my-value', $this->constraint->getValue());
	}

	/**
	 * @return	null
	 */
	public function testGetSqlString()
	{
		$this->assertEquals('default', $this->constraint->getSqlString());
	}

	/**
	 * @returns	null
	 */
	public function testBuildSqlDefault()
	{
		$this->assertEquals(
			"default 'my-value'",
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
		$this->assertEquals(
			"DEFAULT 'my-value'", 
			$this->constraint->buildSql()
		);
	}

	/**
	 * @return	null
	 */
	public function testDefaultNumeric()
	{
		$default = new DefaultValue(99);
		$this->assertEquals(99, $default->getValue());
		
		$expected = 'default 99';
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = 'DEFAULT 99';
		$this->assertEquals($expected, $default->buildSql());
		
	}

	/**
	 * @return	null
	 */
	public function testDefaultObjectSupportingToString()
	{
		$path = new SplFileInfo('my/path');
		$default = new DefaultValue($path);
		$this->assertEquals($path, $default->getValue());
		
		$expected = "default 'my/path'";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = "DEFAULT 'my/path'";
		$this->assertEquals($expected, $default->buildSql());
		
	}

	
	
}

