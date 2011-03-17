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
namespace Test\Appfuel;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Registry,
	StdClass;

/**
 * The Registry is a global object used to hold information that can 
 * easily accessed. There is only one registry but its not a singleton but
 * rather a static class that uses a datastructure to all the data items.
 * Also because we hate globals the registry is used only in the startup 
 * system and then only used when absolutely necessary.
 */
class RegistryTest extends ParentTestCase
{
	/**
	 * Back up the data in the registry
	 * @var string
	 */
	protected $backupData = NULL;

	/**
	 * Backup the registry data then initialize it with an empty bag
	 * @return null
	 */
	public function setUp()
	{
		$this->backupData = Registry::getAll();
		Registry::init();
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		Registry::init($this->backupData);
	}

	/**
	 * These are just a light set of tests because the Registry
	 * wraps a Appfuel\Stdlib\Data\Bag
	 * @return null
	 */
	public function testAddGetExistsCount()
	{
		$this->assertEquals(0, Registry::count());
		$result = Registry::add('label_1', 'this is a string');
		$this->assertTrue($result);

		$this->assertTrue(Registry::exists('label_1'));
		$this->assertEquals(1, Registry::count());
		$this->assertEquals('this is a string', Registry::get('label_1'));

		Registry::add('label_2', 12345);

		$this->assertTrue(Registry::exists('label_2'));
		$this->assertEquals(2, Registry::count());
		$this->assertEquals(12345, Registry::get('label_2'));

		Registry::add('label_3', array(1,2,3,4,5));
		$this->assertTrue(Registry::exists('label_3'));
		$this->assertEquals(array(1,2,3,4,5), Registry::get('label_3'));

		$obj = new StdClass();
		Registry::add('label_4', $obj);
		$this->assertTrue(Registry::exists('label_4'));
		$this->assertEquals($obj, Registry::get('label_4'));

	}

	/**
	 * @return null
	 */
	public function testLoadGetAll()
	{
		$data = array(
			'label_1' => 'value 1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);
		
		$this->assertEquals(0, Registry::count());
		$result = Registry::load($data);
		$this->assertTrue($result);

		$this->assertEquals(count($data), Registry::count());

		$this->assertEquals($data['label_1'], Registry::get('label_1'));
		$this->assertEquals($data['label_2'], Registry::get('label_2'));
		$this->assertEquals($data['label_3'], Registry::get('label_3'));

		$this->assertEquals($data, Registry::getAll());
	}

	/**
	 * @return null
	 */
	public function testIsInitClear()
	{
		/* the registry is initialized but empty */
		$this->assertEquals(0, Registry::count());
		$this->assertTrue(Registry::isInit());	

		$data = array(
			'label_1' => 'value 1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);
		
		Registry::load($data);
		$this->assertEquals(3, Registry::count());
	
		/* 
		 * clear removes the bag and puts the registry in a state
		 * of uninitialized
		 */	
		$result = Registry::clear();
		$this->assertNull($result);
		$this->assertEquals(0, Registry::count());
		$this->assertFalse(Registry::isInit());	

		/* 
		 * lets load again	but this time because the Registry
		 * is unitialized the load fails
		 */	
		$result = Registry::load($data);
		$this->assertFalse($result);
		$this->assertEquals(0, Registry::count());
		
	}

	/**
	 * When using init with no parameters an new bag is created with
	 * no data inside
	 *
	 * @return null
	 */
	public function testInitNoParam()
	{
		Registry::clear();
		$this->assertEquals(0, Registry::count());
		$this->assertFalse(Registry::isInit());	

		$result = Registry::init();
		$this->assertNull($result);
		$this->assertTrue(Registry::isInit());
		$this->assertEquals(0, Registry::count());
	}

	/**
	 * When an array of data is passed into the init, that
	 * array is used to create the bag the Registry is initialized 
	 * with.
	 *
	 * @return null
	 */
	public function testInitArrayParam()
	{
		Registry::clear();
		$this->assertEquals(0, Registry::count());
		$this->assertFalse(Registry::isInit());	

		$data = array(
			'label_1' => 'value 1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);

		$result = Registry::init($data);
		$this->assertNull($result);
		$this->assertTrue(Registry::isInit());
		$this->assertEquals(3, Registry::count());

		$this->assertTrue(Registry::exists('label_1'));
		$this->assertTrue(Registry::exists('label_2'));
		$this->assertTrue(Registry::exists('label_3'));
	}

	/**
	 * When an object that implments the Appfuel\Stdlib\Data\BagInterface
	 * is passed into init then it is used instead of creating one
	 *
	 * @return null
	 */
	public function testIniBagParam()
	{
		Registry::clear();
		$this->assertEquals(0, Registry::count());
		$this->assertFalse(Registry::isInit());	

		$bag = $this->getMock('\\Appfuel\\Stdlib\\Data\\BagInterface');
		
		$countValue = 5;
		$bag->expects($this->any())
			->method('count')
			->will($this->returnValue($countValue));

		$result = Registry::init($bag);
		$this->assertNull($result);
		$this->assertTrue(Registry::isInit());
		$this->assertEquals($countValue, Registry::count());
	}
}

