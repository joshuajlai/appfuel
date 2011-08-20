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
namespace Test\Appfuel\Orm\DataSource;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Source\Db\Map\ColumnMap,
	Appfuel\Framework\DataStructure\ArrayMap;

/**
 * Test the ability to map keys to values and values to keys. Also to provide
 * closures that do the same.
 */
class ArrayMapTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var ColumnMap
	 */
	protected $arrayMap = null;

	/**
	 * Valid array map to pass into the constructor
	 * @var array
	 */
	protected $map = array(
		'id'			=> 'user_id',
		'fistName'		=> 'first_name',
		'lastName'		=> 'last_name',
		'email'			=> 'primary_email'
	);

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->arrayMap = new ArrayMap($this->map);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->map);
	}

	/**
	 * @return array
	 */
	public function provideValidMap()
	{
		$map = array();
		foreach ($this->map as $key => $value) {
			$map[] = array($key, $value);
		}

		return $map;
	}

	/**
	 * @return array
	 */
	public function provideInvalidMaps()
	{	
		return array(
			array(array('key' => array())),
			array(array('key' => array('value'))),
			array(array('key' => 'value', 'key1' => '')),
			array(array('key' => new StdClass())),
		);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\DataStructure\ArrayMapInterface',
			$this->arrayMap
		);
	}

	/**
	 * The map is an immutable array of key value pairs passed into the
	 * constructor
	 *
	 * @return null
	 */
	public function testGetMap()
	{
		$this->assertEquals($this->map, $this->arrayMap->getMap());
	}

	/**
	 * @return null
	 */
	public function testGetKeys()
	{
		$this->assertEquals(
			array_keys($this->map), 
			$this->arrayMap->getKeys()
		);
	}

	/**
	 * @return null
	 */
	public function testGetValues()
	{
		$this->assertEquals(
			array_values($this->map), 
			$this->arrayMap->getValues()
		);
	}

	/**
	 * @dataProvider	provideValidMap
	 * @return	null
	 */
	public function testKeyToValue($key, $value)
	{
		$this->assertEquals($value, $this->arrayMap->keyToValue($key));
		$this->assertFalse($this->arrayMap->keyToValue('is-not-mapped'));
	}

	/**
	 * Anything the is not a valid string or emoty returns false
	 *
	 * @return null
	 */
	public function testKeyToValueInvalidKeys()
	{
		$this->assertFalse($this->arrayMap->keyToValue(''));
		$this->assertFalse($this->arrayMap->keyToValue(array()));
		$this->assertFalse($this->arrayMap->keyToValue(array(1,2,3)));
		$this->assertFalse($this->arrayMap->keyToValue(new StdClass()));
	}

	/**
	 * @dataProvider	provideValidMap
	 * @return	null
	 */
	public function testValueToKey($key, $value)
	{
		$this->assertEquals($key, $this->arrayMap->ValueToKey($value));
		$this->assertFalse($this->arrayMap->ValueToKey('is-not-mapped'));
	}

	/**
	 * Anything the is not a valid string or emoty returns false
	 *
	 * @return null
	 */
	public function testValueToKeyInvalidValues()
	{
		$this->assertFalse($this->arrayMap->valueToKey(''));
		$this->assertFalse($this->arrayMap->valueToKey(array()));
		$this->assertFalse($this->arrayMap->valueToKey(array(1,2,3)));
		$this->assertFalse($this->arrayMap->valueToKey(new StdClass()));
	}

	/**
	 * @dataProvider	provideValidMap
	 * @return	null
	 */
	public function testKeyToValueMapper($key, $value)
	{
		$mapper = $this->arrayMap->getKeyToValueMapper();
		$this->assertInstanceOf('Closure', $mapper);
		$this->assertEquals($value, $mapper($key));
		$this->assertFalse($mapper('is-not-mapped'));
	}

	/**
	 * @dataProvider	provideValidMap
	 * @return	null
	 */
	public function testValueToKeyMapper($key, $value)
	{
		$mapper = $this->arrayMap->getValueToKeyMapper();
		$this->assertInstanceOf('Closure', $mapper);
		$this->assertEquals($key, $mapper($value));
		$this->assertFalse($mapper('is-not-mapped'));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidMaps
	 * @return	null
	 */
	public function testConstructorInvalidMaps($map)
	{
		$map = new ArrayMap($map);
	}
}
