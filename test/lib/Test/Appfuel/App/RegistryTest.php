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
	Appfuel\App\Registry,
	StdClass;

/**
 * The Registry is a global object used to hold information that can be
 * easily accessed. There is only one registry its not a singleton but
 * rather a static class that uses a dictionary to hold all the data items.
 */
class RegistryTest extends ParentTestCase
{
	/**
	 * Back up the data in the registry
	 * @var string
	 */
	protected $backupData = null;

	/**
	 * Backup the registry data then initialize it with an empty bag
	 * @return null
	 */
	public function setUp()
	{
		$this->backupData = Registry::getAll();
		Registry::initialize();
	}

	/**
	 * Restore the original registry data
	 * @return null
	 */
	public function tearDown()
	{
		Registry::initialize($this->backupData);
	}

	/**
	 * These are just a light set of tests because the Registry
	 * wraps a Appfuel\Stdlib\Data\Dictionary
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
	public function testClear()
	{
		/* the registry is initialized but empty */
		$this->assertEquals(0, Registry::count());

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
	}

	/**
	 * When using init with no parameters an new bag is created with
	 * no data inside
	 *
	 * @return null
	 */
	public function testInitializeNoParam()
	{
		Registry::clear();
		$this->assertEquals(0, Registry::count());

		$result = Registry::initialize();
		$this->assertNull($result);
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

		$data = array(
			'label_1' => 'value 1',
			'label_2' => 12345,
			'label_3' => array(1,2,3,4)
		);

		$result = Registry::initialize($data);
		$this->assertNull($result);
		$this->assertEquals(3, Registry::count());

		$this->assertTrue(Registry::exists('label_1'));
		$this->assertTrue(Registry::exists('label_2'));
		$this->assertTrue(Registry::exists('label_3'));
	}

	/**
	 * Test the ability to use an appfuel dictionary interface with initialize
	 *
	 * @return null
	 */
	public function testInitializeDicationaryParam()
	{
		Registry::clear();
		$this->assertEquals(0, Registry::count());

		$dictionary = $this->getMock(
			'\\Appfuel\\Framework\\Data\\DictionaryInterface'
		);
		
		$countValue = 5;
		$dictionary->expects($this->any())
				   ->method('count')
				   ->will($this->returnValue($countValue));

		$result = Registry::initialize($dictionary);
		$this->assertNull($result);
		$this->assertEquals($countValue, Registry::count());
	}

    /**
     * With collect you give the registry a list of keys and it
     * collects all those keys and returns them as a dictionary or array. When
     * a key does not exist it is not included into the bag
     *
     * @return null
     */
    public function testCollect()
    {   
        $data = array(
            'item_str'   => 'value_1',
            'item_int'   => 2,
            'item_true'  => true,
            'item_false' => false,
            'item_null'  => null
        );

        Registry::initialize($data);

        $result = Registry::collect(array_keys($data));
        $this->assertInstanceof(
			'\Appfuel\Framework\Data\DictionaryInterface', 
			$result
		);

        foreach($data as $label => $value) {
            $this->assertTrue($result->exists($label));
            $this->assertEquals($value, $result->get($label));
        }

        /* prove items that don't exist are not included in the bag */
        $result = Registry::collect(array('no_key'));
        $this->assertInstanceof(
			'\Appfuel\Framework\Data\DictionaryInterface', 
			$result
		);
        $this->assertEquals(0, $result->count());

        $returnArray = true;
        $result = Registry::collect(array_keys($data), $returnArray);
        $this->assertInternalType('array', $result);
        $this->assertEquals($data, $result);
    }
}

