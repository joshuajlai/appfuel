<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Config;

use StdClass,
	Appfuel\Config\ConfigRegistry,
	Testfuel\TestCase\BaseTestCase;

class ConfigRegistryTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $backup = array();

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->backup = ConfigRegistry::getAll();
		ConfigRegistry::clear();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		ConfigRegistry::setAll($this->backup);
	}

	/**
	 * @return	array
	 */
	public function provideValidParams()
	{
		return	array(
			array('key',	'str-value'),
			array('key-a',	12345),
			array('key-b',	1.234),
			array('key-c',	array()),
			array('key-d',	array(1,2,3)),
			array('key-e',	array('a'=>'b', 'c' => array(1,2,3))),
			array('key-f',	new StdClass()),
		);
	}

	/**
	 * @return	array
	 */
	public function provideBadKey()
	{
		return array(
			array(0),
			array(1),
			array(100),
			array(-1),
			array(-100),
			array(1.2),
			array(''),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);	
	}

	/**
	 * @test
	 * @dataProvider	provideValidParams
	 * @return	null
	 */
	public function addGetExistConfigItem($key, $value)
	{
		$this->assertEquals(array(), ConfigRegistry::getAll());
		$this->assertFalse(ConfigRegistry::exists($key));
		$this->assertNull(ConfigRegistry::add($key, $value));
		$this->assertTrue(ConfigRegistry::exists($key));
		$this->assertEquals($value, ConfigRegistry::get($key));
	}

	/**
	 * @test
	 * @dataProvider		provideBadKey
	 * @return	null
	 */
	public function addWithBadKey($key)
	{
		$this->setExpectedException('InvalidArgumentException');
		ConfigRegistry::add($key, 'sometext');
	}

	/**
	 * @test
	 * @return	null
	 */
	public function loadData()
	{
		$list = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		$this->assertNull(ConfigRegistry::load($list));
		$this->assertEquals($list, ConfigRegistry::getAll());

		$new = array(
			'param-e' => 'value-5',
			'param-f' => 'value-6'
		);
		$this->assertNull(ConfigRegistry::load($new));

		$expected = array_merge($list, $new);
		$this->assertEquals($expected, ConfigRegistry::getAll());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function loadNotAssociativeArray()
	{
		$this->setExpectedException('InvalidArgumentException');
		$list = array('value-1','value-2','value-3','value-4');
		ConfigRegistry::load($list);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function loadNotAssociativeMixedArray()
	{
		$this->setExpectedException('InvalidArgumentException');
		$list = array('value-1','value-2','value-3',1=>'value-4');
		ConfigRegistry::load($list);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function clearData()
	{
		$list = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		ConfigRegistry::load($list);

		$this->assertNull(ConfigRegistry::clear());
		$this->assertEquals(array(), ConfigRegistry::getAll());
	}

	/**
	 * Set will clear then load
	 *
	 * @test
	 * @return	null
	 */
	public function setAllData()
	{
		$list = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		ConfigRegistry::load($list);

		$new = array(
			'param-e' => 'value-10',
			'param-f' => 'value-12',
			'param-g' => 'value-13',
		);
		ConfigRegistry::setAll($new);

		$this->assertEquals($new, ConfigRegistry::getAll());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function collectData()
	{
		$list = array(
			'param-a' => 'value-1',
			'param-b' => 'value-2',
			'param-c' => 'value-3',
			'param-d' => 'value-4'
		);
		ConfigRegistry::load($list);

		$set  = array('param-a'=> null, 'param-c' => null);
		$data = ConfigRegistry::collect($set);
		
		$expected = array(
			'param-a' => 'value-1',
			'param-c' => 'value-3',
		);
		$this->assertEquals($expected, $data);

		$set  = array('param-a'=> null, 'param-c' => null, 'param-x' => 123);
		$expected = array(
			'param-a' => 'value-1',
			'param-c' => 'value-3',
			'param-x' => 123
		);
		$this->assertEquals($expected, ConfigRegistry::collect($set));

		$set  = array(
			'param-a'=> null, 
			'param-c' => null, 
			'param-x' => 'af-exclude-not-found'
		);
		$expected = array(
			'param-a' => 'value-1',
			'param-c' => 'value-3',
		);
		$this->assertEquals($expected, ConfigRegistry::collect($set));
	}
}

