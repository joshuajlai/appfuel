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
 * Test that a column map can map from key to value
 */
class ColumnMapTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var ColumnMap
	 */
	protected $map = null;

	/**
	 * Valid array map to pass into the constructor
	 * @var array
	 */
	protected $mapArray = array(
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
		$this->map = new ColumnMap($this->mapArray);
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
	public function provideColumnMap()
	{
		$map = array();
		foreach ($this->mapArray as $member => $column) {
			$map[] = array($member, $column);
		}

		return $map;
	}

	/**
	 * @return array
	 */
	public function provideInvalidMaps()
	{	
		return array(
			array(array(123 => 'value')),
			array(array('key' => 'value', 123 => 'value')),
			array(array('key' => array())),
			array(array('key' => array('value'))),
			array(array('key' => 1234)),
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
			'Appfuel\Framework\Orm\Source\Db\Map\ColumnMapInterface',
			$this->map
		);
	}

	/**
	 * The actual map is an immutable structure that can not be altered once
	 * passed into the constructor
	 *
	 * @return null
	 */
	public function estGetMap()
	{
		$this->assertEquals($this->mapArray, $this->map->getMap());	
	}

	/**
	 * @return null
	 */
	public function estGetColumns()
	{
		$this->assertEquals(
			array_values($this->mapArray),
			$this->map->getColumns(),
			'returns a list of database columns'
		);	
	}

	/**
	 * @dataProvider	provideColumnMap
	 * @return null
	 */
	public function estMapColumn($member, $column)
	{
		$this->assertEquals($column, $this->map->mapColumn($member));
	}

	/**
	 * @return null
	 */
	public function estMapColumnMemberNotFound()
	{
		$this->assertFalse($this->map->mapColumn('member-not-found'));
	}

	/**
	 * Anything the is not a valid string or emoty returns false
	 *
	 * @return null
	 */
	public function estMapColumnInvalidMember()
	{
		$this->assertFalse($this->map->mapColumn(''));
		$this->assertFalse($this->map->mapColumn(array()));
		$this->assertFalse($this->map->mapColumn(array(1,2,3)));
		$this->assertFalse($this->map->mapColumn(12345));
		$this->assertFalse($this->map->mapColumn(0));
		$this->assertFalse($this->map->mapColumn(new StdClass()));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidMaps
	 * @return	null
	 */
	public function estConstructorInvalidMaps($map)
	{
		$map = new ColumnMap($map);
	}
}
