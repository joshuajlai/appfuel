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
	Appfuel\Orm\Source\Db\Map\ColumnMap;

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
	protected $mapArray = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->mapArray = array(
			'user_id'		=> 'id',
			'fist_name'		=> 'firstName',
			'last_name'		=> 'lastName',
			'primary_email' => 'email'
		);

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
	public function testGetMap()
	{
		$this->assertEquals($this->mapArray, $this->map->getMap());	
	}
}
