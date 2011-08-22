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
namespace TestFuel\Framework\DataStructure;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * A data dictionary is a general data structure used to manage an associative 
 * array. The primary focus of this structure is to remove the need to check 
 * if the keys exists. It also provides the ability to return a default value 
 * when the key you are looking for does not exist. 
 */
class DictionaryTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Data\Dictionary
	 */
	protected $dictionary = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->dictionary = new Dictionary();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->dictionary);
	}

	/**
	 * Test adding different types of data
	 *
	 * @return null
	 */
	public function testAddGetExistsCount()
	{
		
		$this->assertEquals(0, $this->dictionary->count());
		
		/* add a string */
		$this->assertFalse($this->dictionary->exists('label_1'));
		$result = $this->dictionary->add('label_1', 'this is a string');
		$this->assertSame($this->dictionary, $result);
		$this->assertEquals(1, $this->dictionary->count());
		$this->assertTrue($this->dictionary->exists('label_1'));
		$this->assertEquals(
			'this is a string', 
			$this->dictionary->get('label_1')
		);

		/* add an integer */		
		$this->assertFalse($this->dictionary->exists('label_2'));
		$result = $this->dictionary->add('label_2', 12345);
		$this->assertTrue($this->dictionary->exists('label_2'));
		$this->assertEquals(2, $this->dictionary->count());
		$this->assertEquals(12345, $this->dictionary->get('label_2'));

		/* add an array */
		$this->assertFalse($this->dictionary->exists('label_3'));
		$result = $this->dictionary->add('label_3', array(1,2,3,4,5));
		$this->assertTrue($this->dictionary->exists('label_3'));
		$this->assertEquals(3, $this->dictionary->count());
		$this->assertEquals(
			array(1,2,3,4,5), 
			$this->dictionary->get('label_3')
		);

		/* add an object */
		$this->assertFalse($this->dictionary->exists('label_4'));
		$obj = new StdClass();
		$result = $this->dictionary->add('label_4', $obj);
		$this->assertTrue($this->dictionary->exists('label_4'));
		$this->assertEquals(4, $this->dictionary->count());
		$this->assertSame($obj, $this->dictionary->get('label_4'));
	}

	/**
	 * Test what happens when you and the same label twice with different
	 * values
	 *
	 * @return null
	 */
	public function testAddOverride()
	{
		$this->assertEquals(0, $this->dictionary->count());
		$value_1 = 12345;
		$value_2 = 'this is a string';
		$label   = 'label_1';

		/* normal add */
		$this->dictionary->add($label, $value_1);
		$this->assertEquals(1, $this->dictionary->count());
		$this->assertTrue($this->dictionary->exists($label));
		$this->assertEquals($value_1, $this->dictionary->get($label));	

		/* override label with new value */
		$this->dictionary->add($label, $value_2);
		$this->assertEquals(1, $this->dictionary->count());
		$this->assertTrue($this->dictionary->exists($label));
		$this->assertEquals($value_2, $this->dictionary->get($label));	
	}

	/**
	 * Test the ability for get method to return given defaults
	 *
	 * @return null
	 */
	public function testGetDefaultValues()
	{
		$this->assertEquals(0, $this->dictionary->count());
		$this->assertFalse($this->dictionary->exists('nothing'));

		$this->assertNull($this->dictionary->get('nothing'));
		$this->assertEquals(
			array(), 
			$this->dictionary->get('nothing', array())
		);
		$this->assertEquals('test', $this->dictionary->get('nothing', 'test'));
		$this->assertEquals(12345, $this->dictionary->get('nothing', 12345));

		$obj = new StdClass();
		$this->assertEquals($obj, $this->dictionary->get('nothing', $obj));
	}

	/**
	 * @return null
	 */
	public function testGetAllLoad()
	{
		$this->assertEquals(0, $this->dictionary->count());
		$data = array(
			'label_1' => 'value_1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);

		$result = $this->dictionary->load($data);
		$this->assertSame($this->dictionary, $result);
		$this->assertEquals(count($data), $this->dictionary->count());
		$this->assertEquals($data, $this->dictionary->getAll());
	}
}
