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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Schema\SchemaObject,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * Generic object that holds a list of attributes. Attributes are only set
 * via the constructor
 */
class SchemaObjectTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var SchemaObject
	 */
	protected $object = null;

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
			'attr1'	=> 'value a',
			'attr2' => 12345,
			'attr3'	=> true,
		);

		$this->object = new SchemaObject($this->attrs);
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
			'Appfuel\Framework\Db\Schema\SchemaObjectInterface',
			$this->object,
			'must implment this interface'
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
		$this->assertTrue($this->object->isAttribute('attr1'));
		$this->assertTrue($this->object->isAttribute('attr2'));
		$this->assertTrue($this->object->isAttribute('attr3'));
		$this->assertEquals(
			$this->attrs['attr1'], 
			$this->object->getAttribute('attr1')
		);
		$this->assertEquals(
			$this->attrs['attr2'], 
			$this->object->getAttribute('attr2')
		);
		$this->assertEquals(
			$this->attrs['attr3'], 
			$this->object->getAttribute('attr3')
		);

		$label   = 'not-there';
		$default = null;
		/* look for something know not to exist */
		$this->assertFalse($this->object->isAttribute($label));

		$this->assertNull($this->object->getAttribute($label));
		$this->assertNull($this->object->getAttribute($label, $default));

		$default = 'this is a string';
		$this->assertEquals(
			$default, 
			$this->object->getAttribute($label, $default)
		);

		$default = array(1,2,3);;
		$this->assertEquals(
			$default, 
			$this->object->getAttribute($label, $default)
		);

		$default = new StdClass();
		$this->assertEquals(
			$default, 
			$this->object->getAttribute($label, $default)
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

		$object = new SchemaObject($attr);
		$this->assertTrue($object->isAttribute('attrA'));
		$this->assertTrue($object->isAttribute('attrB'));
		$this->assertTrue($object->isAttribute('attrC'));

		$this->assertEquals('valueA', $object->getAttribute('attrA'));
		$this->assertEquals(12345, $object->getAttribute('attrB'));
		$this->assertEquals(array(1,2,3), $object->getAttribute('attrC'));
	}

}
