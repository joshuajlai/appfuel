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
namespace Test\Appfuel\Stdlib\Data;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\Stdlib\BagInterface,
	Appfuel\Stdlib\Data\Bag,
	StdClass;

/**
 * A data bag is a general data structure used to manage an associative array. 
 * The primary focus of this structure is to remove the need to check if the 
 * keys exists. It also provides the ability to return a default value when
 * the key you are looking for does not exist. 
 */
class BagTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Stdlib\Data\Bag
	 */
	protected $bag = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->bag = new Bag();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->bag);
	}

	/**
	 * Test adding different types of data
	 *
	 * @return null
	 */
	public function testAddGetExistsCount()
	{
		
		$this->assertEquals(0, $this->bag->count());
		
		/* add a string */
		$this->assertFalse($this->bag->exists('label_1'));
		$result = $this->bag->add('label_1', 'this is a string');
		$this->assertSame($this->bag, $result);
		$this->assertEquals(1, $this->bag->count());
		$this->assertTrue($this->bag->exists('label_1'));
		$this->assertEquals('this is a string', $this->bag->get('label_1'));

		/* add an integer */		
		$this->assertFalse($this->bag->exists('label_2'));
		$result = $this->bag->add('label_2', 12345);
		$this->assertTrue($this->bag->exists('label_2'));
		$this->assertEquals(2, $this->bag->count());
		$this->assertEquals(12345, $this->bag->get('label_2'));

		/* add an array */
		$this->assertFalse($this->bag->exists('label_3'));
		$result = $this->bag->add('label_3', array(1,2,3,4,5));
		$this->assertTrue($this->bag->exists('label_3'));
		$this->assertEquals(3, $this->bag->count());
		$this->assertEquals(array(1,2,3,4,5), $this->bag->get('label_3'));

		/* add an object */
		$this->assertFalse($this->bag->exists('label_4'));
		$obj = new StdClass();
		$result = $this->bag->add('label_4', $obj);
		$this->assertTrue($this->bag->exists('label_4'));
		$this->assertEquals(4, $this->bag->count());
		$this->assertSame($obj, $this->bag->get('label_4'));
	}

	/**
	 * Test what happens when you and the same label twice with different
	 * values
	 *
	 * @return null
	 */
	public function testAddOverride()
	{
		$this->assertEquals(0, $this->bag->count());
		$value_1 = 12345;
		$value_2 = 'this is a string';
		$label   = 'label_1';

		/* normal add */
		$this->bag->add($label, $value_1);
		$this->assertEquals(1, $this->bag->count());
		$this->assertTrue($this->bag->exists($label));
		$this->assertEquals($value_1, $this->bag->get($label));	

		/* override label with new value */
		$this->bag->add($label, $value_2);
		$this->assertEquals(1, $this->bag->count());
		$this->assertTrue($this->bag->exists($label));
		$this->assertEquals($value_2, $this->bag->get($label));	
	}

	/**
	 * Test the ability for get method to return given defaults
	 *
	 * @return null
	 */
	public function testGetDefaultValues()
	{
		$this->assertEquals(0, $this->bag->count());
		$this->assertFalse($this->bag->exists('nothing'));

		$this->assertNull($this->bag->get('nothing'));
		$this->assertEquals(array(), $this->bag->get('nothing', array()));
		$this->assertEquals('test', $this->bag->get('nothing', 'test'));
		$this->assertEquals(12345, $this->bag->get('nothing', 12345));

		$obj = new StdClass();
		$this->assertEquals($obj, $this->bag->get('nothing', $obj));
	}

	/**
	 * @return null
	 */
	public function testGetAllLoad()
	{
		$this->assertEquals(0, $this->bag->count());
		$data = array(
			'label_1' => 'value_1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);

		$result = $this->bag->load($data);
		$this->assertSame($this->bag, $result);
		$this->assertEquals(count($data), $this->bag->count());
		$this->assertEquals($data, $this->bag->getAll());
	}

}
