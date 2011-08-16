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

use Test\AfTestCase as ParentTestCase;

/**
 * The AbstractType handle common functionality to all int data types
 */
class AbstractIntTypeTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var AbstractIntType
	 */
	protected $type = null;

	/**
	 * Name used in sql statements first param for constructor
	 * @var string
	 */
	protected $sqlName = null;

	/**
	 * Unsigned max integer second param for constructor
	 * @var int
	 */
	protected $umax = null;

	/**
	 * Signed min integer third param for constructor
	 * @var int
	 */
	protected $min = null;

	/**
	 * Signed max integer forth param for constructor
	 * @var int
	 */
	protected $max = null;
	
	/**
	 * Flag used to determine if the type is signed or unsigned last param
	 * @var bool
	 */
	protected $isUnsigned = null;

	/**
	 * Used to create the mock classes
	 * @var string
	 */
	protected $typeClass = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->typeClass = 'Appfuel\Db\Mysql\DbObject\DataType\Number' . 
						   '\AbstractIntType';

		$this->sqlName = 'tinyint';
		$this->umax = 255;
		$this->min = -128;
		$this->max = 127;
		$this->isUnsigned = true;

		$params = array(
			$this->sqlName,
			$this->umax,
			$this->min,
			$this->max,
			$this->isUnsigned
		);
		
		$this->type = $this->getMockBuilder($this->typeClass)
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
	 * @return null
	 */
	public function testUminMmax()
	{
		/* unsigned min is always 0 */
		$this->assertEquals(0, $this->type->getUmin());
		$this->assertEquals($this->umax, $this->type->getUmax());
	}

	/**
	 * @return	null
	 */
	public function testSqlName()
	{
		$this->assertEquals($this->sqlName, $this->type->getSqlName());
	}

	/**
	 * @return	null
	 */
	public function testMaxMin()
	{
		$this->assertEquals($this->min, $this->type->getMin());
		$this->assertEquals($this->max, $this->type->getMax());
	}

	/**
	 * @return	null
	 */
	public function testEnableUnsigned()
	{
		$this->assertTrue($this->type->isUnsigned());
		$this->assertSame(
			$this->type,
			$this->type->disableUnsigned(),
			'exposes a fluent interface'
		);
		
		$this->assertFalse($this->type->isUnsigned());
	
		$this->assertSame(
			$this->type,
			$this->type->enableUnsigned(),
			'exposes a fluent interface'
		);
		$this->assertTrue($this->type->isUnsigned());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testUmaxLessThanZero() 
	{
		$params = array(
			'tinyint',
			-122,
			-128,
			255,
			true
		);
		
		$this->type = $this->getMockBuilder($this->typeClass)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testMinGreaterThanZero() 
	{
		$params = array(
			'tinyint',
			127,
			122,
			255,
			true
		);
		
		$this->type = $this->getMockBuilder($this->typeClass)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testMaxLessThanZero() 
	{
		$params = array(
			'tinyint',
			127,
			-128,
			-122,
			true
		);
		
		$this->type = $this->getMockBuilder($this->typeClass)
						   ->setConstructorArgs($params)
						   ->getMockForAbstractClass();
	}
}
