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
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the common functionality of all contraints
 */
class AbstractConstraintTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AbstractContraint
	 */
	protected $constraint = null;

	/**
	 * Parameters used to describe the type
	 * @var array
	 */
	protected $sqlString = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->sqlString = 'not null';
		$params = array($this->sqlString);
		$class = 'Appfuel\Db\Mysql\Constraint\AbstractConstraint';
		$this->constraint = $this->getMockBuilder($class)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
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
		$this->assertEquals(
			$this->sqlString, 
			$this->constraint->getSqlString()
		);
	}

	/**
	 * This used to determine if the sql string will be returned all uppercase 
	 * or all lowercase
	 *
	 * @return	null
	 */
	public function testIsEnableDisableUpperCase()
	{
		$this->assertFalse($this->constraint->isUpperCase());
		$this->assertSame(
			$this->constraint,
			$this->constraint->enableUpperCase(),
			'uses a fluent interface'
		);

		$this->assertTrue($this->constraint->isUpperCase());
	
		$this->assertSame(
			$this->constraint,
			$this->constraint->disableUppercase(),
			'uses a fluent interface'
		);

		$this->assertFalse($this->constraint->isUpperCase());
	}

	/**
	 * @depends	testIsEnableDisableUpperCase
	 */
	public function testBuildSql()
	{
		$this->assertEquals(
			strtolower($this->sqlString), 
			$this->constraint->buildSql(),
			'unless isUppercase is false so should be lowercase'
		);
	
		$this->constraint->enableUppercase();
		
		$this->assertEquals(
			strtoupper($this->sqlString), 
			$this->constraint->buildSql(),
			'should be all uppercase'
		);	
	}

	/**
	 * Sql string can not be empty	
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorSqlEmptyString_Failure()
	{
        $params = array('');
        $class = 'Appfuel\Db\Mysql\Constraint\AbstractConstraint';
        $constraint = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Sql string can not be int
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorSqlInt_Failure()
	{
        $params = array(12345);
        $class = 'Appfuel\Db\Mysql\Constraint\AbstractConstraint';
        $contraint = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Sql string can not be an array
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorSqlArray_Failure()
	{
        $params = array(array(1,2,3));
        $class = 'Appfuel\Db\Mysql\Constraint\AbstractConstraint';
        $constraint = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Sql string can not be an object
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorSql_ObjectFailure()
	{
        $params = array(new StdClass());
        $class = 'Appfuel\Db\Mysql\Constraint\AbstractConstraint';
        $constraint = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}
}

