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
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * The AbstractType handle common functionality to all data types
 */
class AbstractTypeTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AbstractType
	 */
	protected $type = null;

	/**
	 * Parameters used to describe the type
	 * @var array
	 */
	protected $sqlString = null;

	/**
	 * @var Dictionary
	 */
	protected $attrs = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$attrs = array('unsigned' => true);
		$this->attrs = new Dictionary($attrs);
		$this->sqlString = 'TINYINT';
		$this->validator = 'tinyint-type';

		$params = array($this->sqlString, $this->validator, $this->attrs);
		$class = 'Appfuel\Db\Mysql\DataType\AbstractType';
		$this->type = $this->getMockBuilder($class)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->type);
	}

	/**
	 * @return	null
	 */
	public function testGetSqlString()
	{
		$this->assertEquals($this->sqlString, $this->type->getSqlString());
	}

	/**
	 * @return	null
	 */
	public function testGetValidatorName()
	{
		$this->assertEquals($this->validator, $this->type->getValidatorName());
	}

	/**
	 * @return	null
	 */
	public function testGetAttribute()
	{
		$this->assertTrue($this->type->getAttribute('unsigned'));

		/* attr we know not to be there */
		$this->assertNull($this->type->getAttribute('no-attr'));

		/* make sure default work correctly */
		$this->assertEquals(
			'default', 
			$this->type->getAttribute('no-attr', 'default'),
			'returns second param when first param is not found'
		);
	}

	/**
	 * This controls the attribute used to determine if the sql string
	 * will be returned all uppercase or all lowercase
	 *
	 * @return	null
	 */
	public function testEnableDisableUppercase()
	{
		$this->assertNull($this->type->getAttribute('isUppercase'));
		$this->assertSame(
			$this->type,
			$this->type->enableUppercase(),
			'uses a fluent interface'
		);

		$this->assertTrue($this->type->getAttribute('isUppercase'));
	
		$this->assertSame(
			$this->type,
			$this->type->disableUppercase(),
			'uses a fluent interface'
		);

		$this->assertFalse($this->type->getAttribute('isUppercase'));
			
	}

	/**
	 * @depends	testEnableDisableUppercase
	 */
	public function testBuildSql()
	{
		$this->assertEquals(
			strtolower($this->sqlString), 
			$this->type->buildSql(),
			'unless isUppercase is false so should be lowercase'
		);
	
		$this->type->enableUppercase();
		
		$this->assertEquals(
			strtoupper($this->sqlString), 
			$this->type->buildSql(),
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
        $params = array('', $this->validator, $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
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
        $params = array(12345, $this->validator, $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
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
        $params = array(array(1,2,3), $this->validator, $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
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
        $params = array(new StdClass(), $this->validator, $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Name of the validator can not be an empty string
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorValidator_EmptyStringFailure()
	{
        $params = array($this->sqlString, '', $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Name of the validator can not be an integer
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorValidator_IntFailure()
	{
        $params = array($this->sqlString, 12345, $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Name of the validator can not be an array
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorValidator_ArrayFailure()
	{
        $params = array($this->sqlString, array(1,3,4), $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}

	/**
	 * Name of the validator can not be an object
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorValidator_ObjectFailure()
	{
        $params = array($this->sqlString, new StdClass(), $this->attrs);
        $class = 'Appfuel\Db\Mysql\DataType\AbstractType';
        $type = $this->getMockBuilder($class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
	}
}

