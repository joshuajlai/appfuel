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
	 * @return null
	 */
	public function setUp()
	{
		$this->column = new Column();
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
}
