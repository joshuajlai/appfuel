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
namespace TestFuel\Test\Db\Schema\Column;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Db\Schema\Table\Column;

/**
 */
class ColumnTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Column
	 */
	protected $column = null;

	/**
	 * First parameter of the constructor. Then name of the column
	 * @var string
	 */
	protected $name = null;

	/**
	 * Second parameter of the constructor. The data type of the column
	 * @var DataTypeInterface
	 */
	protected $dataType = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->name = 'my-column';
		$interface = 'Appfuel\Framework\Db\Schema\Table\DataTypeInterface';
		$this->dataType = $this->getMock($interface);

		$this->column = new Column($this->name, $this->dataType);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->column = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Schema\Table\ColumnInterface',
			$this->column
		);
	}

	/**
	 * Immutable members are name and dataType
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testImmutableMembers()
	{
		$this->assertEquals($this->name, $this->column->getName());
		$this->assertEquals($this->dataType, $this->column->getDataType());
	}
}
