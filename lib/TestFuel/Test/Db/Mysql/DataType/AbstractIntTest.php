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
 * The AbstractType handle common functionality for integer types
 */
class AbstractTypeIntTest extends BaseTestCase
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
	 * AbstractInt class
	 * @var string
	 */
	protected $class = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
        $this->class = 'Appfuel\Db\Mysql\DataType\AbstractInt';
		$this->sqlString = 'tinyint';
		$this->validator = 'tinyint-type';

		$params = array($this->sqlString, $this->validator);
		$this->type = $this->getMockBuilder($this->class)
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
	 * Text used in sql string
	 * 
	 * @return	null
	 */
	public function testStringsUsedInSql()
	{
		$this->assertEquals('unsigned', $this->type->getSqlUnsigned());
		$this->assertEquals('zerofill', $this->type->getSqlZeroFill());
		$this->assertEquals(
			'auto_increment', 
			$this->type->getSqlAutoIncrement()
		);
	
		/* passed in from the constructor */
		$this->assertEquals($this->sqlString, $this->type->getSqlString());
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableAutoIncrement()
	{
		$this->assertFalse($this->type->isAutoIncrement());
		$this->assertSame(
			$this->type, 
			$this->type->enableAutoIncrement(),
			'uses a fluent interface'
		);
		$this->assertTrue($this->type->isAutoIncrement());

		$this->assertSame(
			$this->type, 
			$this->type->disableAutoIncrement(),
			'uses a fluent interface'
		);
		$this->assertFalse($this->type->isAutoIncrement());
	}

	/**
	 * @return null
	 */
	public function testIsUnsignedEnableUnsigned()
	{
		$this->assertFalse($this->type->isUnsigned());
		$this->assertSame($this->type, $this->type->enableUnsigned());
		$this->assertTrue($this->type->isUnsigned());

		/* you disable unsigned by enabling signed 
		 * no check for signed because when isUnsigned is false it means
		 * signed is enabled
		 */
		$this->assertSame($this->type, $this->type->enableSigned());
		$this->assertFalse($this->type->isUnsigned());
		
	}

	/**
	 * @return	null
	 */
	public function testGetSetDisplayWidth()
	{
		/* means it has not been set */
		$this->assertNull($this->type->getDisplayWidth());

		$width = 5;
		$this->assertSame($this->type, $this->type->setDisplayWidth($width));
		$this->assertEquals($width, $this->type->getDisplayWidth());
	
		$width = 0;
		$this->assertSame($this->type, $this->type->setDisplayWidth($width));
		$this->assertEquals($width, $this->type->getDisplayWidth());
	
	}

	/**
	 * @return null
	 */
	public function testIsEnableDisableZeroFill()
	{
		$this->assertFalse($this->type->isZeroFill());
		$this->assertSame($this->type, $this->type->enableZeroFill());
		$this->assertTrue($this->type->isZeroFill());

		$this->assertSame($this->type, $this->type->disableZeroFill());
		$this->assertFalse($this->type->isZeroFill());
	}

	/**
	 * @return	null
	 */
	public function testBuildSqlNoAttrsLowerCaseUpperCase()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->assertEquals(
			strtolower($this->type->getSqlString()),
			$this->type->buildSql(),
			'should be the same as the lower case sql string'
		);

		$this->type->enableUpperCase();
		$this->assertEquals(
			strtoupper($this->type->getSqlString()),
			$this->type->buildSql(),
			'should be the same as the lower case sql string'
		);
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return null
	 */
	public function testBuildSqlUnsigned()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned();
		
		$expected = "{$this->type->getSqlString()} unsigned";
		$this->assertEquals($expected, $this->type->buildSql());


		$this->type->enableUpperCase();

		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testbuildSqlZeroFill()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableZeroFill();
		
		$expected = "{$this->type->getSqlString()} zerofill";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testbuildSqlAutoIncrement()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableAutoIncrement();
		
		$expected = "{$this->type->getSqlString()} auto_increment";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testBuildSqlUnsignedZeroFill()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned()
				   ->enableZeroFill();
		
		$expected = "{$this->type->getSqlString()} unsigned zerofill";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testBuildSqlUnsignedAutoIncrement()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned()
				   ->enableAutoIncrement();
		
		$expected = "{$this->type->getSqlString()} unsigned auto_increment";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testBuildSqlUnsignedDisplayWidth()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned()
				   ->setDisplayWidth(4);
		
		$expected = "{$this->type->getSqlString()}(4) unsigned";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testBuildSqlUnsignedZeroFillAutoIncrement()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned()
				   ->enableZeroFill()
				   ->enableAutoIncrement();
		
		$expected = "{$this->type->getSqlString()} unsigned zerofill ";
		$expected .= "auto_increment";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * @depends	testBuildSqlNoAttrsLowerCaseUpperCase
	 * @return	null
	 */
	public function testBuildSqlUnsignedZeroFillAutoIncrementDisplayWidth()
	{
		$this->assertFalse($this->type->isUpperCase());
		$this->type->enableUnsigned()
				   ->enableZeroFill()
				   ->enableAutoIncrement()
				   ->setDisplayWidth(4);
		
		$expected = "{$this->type->getSqlString()}(4) unsigned zerofill ";
		$expected .= "auto_increment";
		$this->assertEquals($expected, $this->type->buildSql());

		$this->type->enableUpperCase();
		$this->assertEquals(strtoupper($expected), $this->type->buildSql());
	}

	/**
	 * Test the third parameter which toggle the signed/unsigned 
	 *
	 * @return	null
	 */
	public function testConstructorAttrStringUnsigned()
	{

		$attrs = 'unsigned';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isUnsigned());
	}

	/**
	 * @return	null
	 */
	public function testConstructorAttrDisplayWidth()
	{
		$displayWidth = 5; 
		$attrs = '5'; 
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertEquals($displayWidth, $type->getDisplayWidth());

		$displayWidth = 0; 
        $params[2] = '0';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();
		
		$this->assertEquals($displayWidth, $type->getDisplayWidth());
	}

	/**
	 * @return	null
	 */
	public function testConstructor5thParamZeroFill()
	{
		$attrs  = 'zerofill'; 
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isZeroFill());

        $params[2] = 'not-zero-fill';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertFalse($type->isZeroFill());

        $params[2] = 'ZEROFILL';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isZeroFill());

        $params[2] = 'ZeroFILL';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isZeroFill());
	}

	/**
	 * @return	null
	 */
	public function testConstructor6thIsAutoIncrement()
	{
		$attrs	= 'auto_increment';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isAutoIncrement());

        $params[2] = 'not-auto-increment';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertFalse($type->isAutoIncrement());

        $params[2] = 'AUTO_INCREMENT';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isAutoIncrement());

        $params[2] = 'AUTo_IncremenT';
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isAutoIncrement());
	}

	/**
	 * @return	null
	 */
	public function testConstructorAllParamsFilled()
	{
		$displayWidth = 24;
		$attrs = '24 unsigned zerofill auto_increment';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isUnsigned());
		$this->assertEquals($displayWidth, $type->getDisplayWidth());
		$this->assertTrue($type->isZeroFill());
		$this->assertTrue($type->isAutoIncrement());
	}

	/**
	 * @return	null
	 */
	public function testConstructorAllParamsFilledWithSpaces()
	{
		$displayWidth = 24;
		$attrs = ' 24   unsigned  zerofill  auto_increment ';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertTrue($type->isUnsigned());
		$this->assertEquals($displayWidth, $type->getDisplayWidth());
		$this->assertTrue($type->isZeroFill());
		$this->assertTrue($type->isAutoIncrement());
	}

	/**
	 * @return	null
	 */
	public function testConstructorAttrJustSpacesString()
	{
		$attrs = ' ';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertFalse($type->isUnsigned());
		$this->assertNull($type->getDisplayWidth());
		$this->assertFalse($type->isZeroFill());
		$this->assertFalse($type->isAutoIncrement());
	}

	/**
	 * @return	null
	 */
	public function testConstructorAttrEmptyString()
	{
		$attrs = '';
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

		$this->assertFalse($type->isUnsigned());
		$this->assertNull($type->getDisplayWidth());
		$this->assertFalse($type->isZeroFill());
		$this->assertFalse($type->isAutoIncrement());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */	
	public function testConstructor4thParamDisplayWidth_Failure()
	{
		$attrs = '-5'; 
        $params = array($this->sqlString, $this->validator, $attrs);
        $type = $this->getMockBuilder($this->class)
                     ->setConstructorArgs($params)
                     ->getMockForAbstractClass();

	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetDisplayNotInt_EmptyStringFailure()
	{
		$this->type->setDisplayWidth('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetDisplayNotInt_NonEmptyStringFailure()
	{
		$this->type->setDisplayWidth('this is a string');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetDisplayNotInt_ArrayFailure()
	{
		$this->type->setDisplayWidth(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetDisplayNotInt_ObjFailure()
	{
		$this->type->setDisplayWidth(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetDisplayNotInt_IntLessThanZeroFailure()
	{
		$this->type->setDisplayWidth(-123);
	}
}
