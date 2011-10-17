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
namespace TestFuel\Test\Db\Schema;

use StdClass,
	Appfuel\Db\Schema\DataType,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * The schema datatype describe an vendor agnostic model of the data type.
 * we will test its interface here.
 */
class DataTypeTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DataType
	 */
	protected $type = null;

	/**
	 * Associative array used in the constructor
	 * @var array
	 */
	protected $attrs = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->attrs = array(
			'type-name'		=> 'integer',
			'type-modifier' => 6,
			'unsigned'		=> true,
		);

		$this->type = new DataType($this->attrs);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->type = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\DataTypeInterface',
			$this->type,
			'must implment this interface'
		);
	}

	/**
	 * @depends	testInterface
	 */
	public function testGetName()
	{
		$this->assertEquals($this->attrs['type-name'], $this->type->getName());
	}

	/**
	 * @depends	testInterface
	 */
	public function testIsGetModifier()
	{
		$this->assertTrue($this->type->isTypeModifier());
		$this->assertEquals(
			$this->attrs['type-modifier'], 
			$this->type->getTypeModifier()
		);
	}

	/**
	 * In setUp we only added on attribute so we will test that it 
	 * exists and we can get it
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsGetAttribute()
	{
		$this->assertTrue($this->type->isAttribute('unsigned'));
		$this->assertEquals(true, $this->type->getAttribute('unsigned'));

		$label   = 'not-there';
		$default = null;
		/* look for something know not to exist */
		$this->assertFalse($this->type->isAttribute($label));

		$this->assertNull($this->type->getAttribute($label));
		$this->assertNull($this->type->getAttribute($label, $default));

		$default = 'this is a string';
		$this->assertEquals(
			$default, 
			$this->type->getAttribute($label, $default)
		);

		$default = array(1,2,3);;
		$this->assertEquals(
			$default, 
			$this->type->getAttribute($label, $default)
		);

		$default = new StdClass();
		$this->assertEquals(
			$default, 
			$this->type->getAttribute($label, $default)
		);	
	}

	/**
	 * @return	null
	 */
	public function testConstructorDictionaryInterface()
	{
		$attr = new Dictionary(array(
			'type-name' => 'enum',
			'type-modifier' => array('a', 'b', 'c'),
			'attrA'			=> 'valueA',
			'attrB'			=> 12345,
			'attrC'			=> array(1,2,3) 
		));

		$type = new DataType($attr);
		$this->assertEquals('enum', $type->getName());
		$this->assertTrue($type->isTypeModifier());
		$this->assertEquals(array('a','b','c'), $type->getTypeModifier());
		$this->assertTrue($type->isAttribute('attrA'));
		$this->assertTrue($type->isAttribute('attrB'));
		$this->assertTrue($type->isAttribute('attrC'));

		$this->assertEquals('valueA', $type->getAttribute('attrA'));
		$this->assertEquals(12345, $type->getAttribute('attrB'));
		$this->assertEquals(array(1,2,3), $type->getAttribute('attrC'));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorNoTypeModifier()
	{
		$attrs = array('type-name' => 'integer');
		$type  = new DataType($attrs);
		$this->assertFalse($type->isTypeModifier());
		$this->assertNull($type->getTypeModifier());
		
		$attrs = array('type-name' => 'integer', 'type-modifier' => '');
		$type  = new DataType($attrs);
		$this->assertFalse($type->isTypeModifier());
		$this->assertNull($type->getTypeModifier());
		
		$attrs = array('type-name' => 'integer', 'type-modifier' => false);
		$type  = new DataType($attrs);
		$this->assertFalse($type->isTypeModifier());
		$this->assertNull($type->getTypeModifier());
		
		$attrs = array('type-name' => 'integer', 'type-modifier' => array());
		$type  = new DataType($attrs);
		$this->assertFalse($type->isTypeModifier());
		$this->assertNull($type->getTypeModifier());
	
		$attrs = array('type-name' => 'integer', 'type-modifier' => 0);
		$type  = new DataType($attrs);
		$this->assertFalse($type->isTypeModifier());
		$this->assertNull($type->getTypeModifier());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_EmptyArrayFailure()
	{
		$type = new DataType(array());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_EmptyDictionaryFailure()
	{
		$type = new DataType(new Dictionary(array()));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_EmptyStringFailure()
	{
		$type = new DataType(array('type-name' => ''));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_FalseFailure()
	{
		$type = new DataType(array('type-name' => false));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_TrueFailure()
	{
		$type = new DataType(array('type-name' => true));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_ArrayFailure()
	{
		$type = new DataType(array('type-name' => array()));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testConstructorName_ObjectFailure()
	{
		$type = new DataType(array('type-name' => new StdClass()));
	}



}
