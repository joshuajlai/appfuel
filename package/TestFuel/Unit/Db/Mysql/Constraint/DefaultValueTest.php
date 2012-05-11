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
	public function testGetSqlPhrase()
	{
		$this->assertEquals('default', $this->constraint->getSqlPhrase());
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
	public function testDefaultInt()
	{
		$value = 99;
		$default = new DefaultValue($value);
		$this->assertEquals($value, $default->getValue());
		
		$expected = "default $value";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = strtoupper($expected);
		$this->assertEquals($expected, $default->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testDefaultFloat()
	{
		$value = 99.99;
		$default = new DefaultValue($value);
		$this->assertEquals($value, $default->getValue());
		
		$expected = "default $value";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = strtoupper($expected);
		$this->assertEquals($expected, $default->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testDefaultString()
	{
		$value = 'some value';
		$default = new DefaultValue($value);
		$this->assertEquals($value, $default->getValue());
		
		$expected = "default '$value'";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = "DEFAULT '$value'";
		$this->assertEquals($expected, $default->buildSql());
	}

	/**
	 * @return	null
	 */
	public function testDefaultEmptyString()
	{
		$value = '';
		$default = new DefaultValue($value);
		$this->assertEquals($value, $default->getValue());
		
		$expected = "default ''";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = "DEFAULT ''";
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

	/**
	 * @return	null
	 */
	public function testDefaultNull()
	{
		$default = new DefaultValue(null);
		$this->assertNull($default->getValue());
		
		$expected = "default null";
		$this->assertEquals($expected, $default->buildSql());

		$default->enableUpperCase();
		$expected = "DEFAULT NULL";
		$this->assertEquals($expected, $default->buildSql());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testDefaultObjectNotSupportingToString_Failure()
	{
		$default = new DefaultValue(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testDefaultArray_Failure()
	{
		$default = new DefaultValue(array(1,2,3));
	}	
}

