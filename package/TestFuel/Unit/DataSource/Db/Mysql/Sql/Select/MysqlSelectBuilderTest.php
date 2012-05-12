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
namespace TestFuel\Unit\DataSource\Db\Mysql\Sql\Select;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\Mysql\Sql\Select\MysqlSelectBuilder;

/**
 */
class SelectBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var SelectBuilder
	 */
	protected $builder= null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->builder	= new MysqlSelectBuilder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->builder = null;
	}

	/**
	 * @return SelectBuilder
	 */
	public function getSelectBuilder()
	{
		return $this->builder;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$builder = $this->getSelectBuilder();
		$interface = 'Appfuel\DataSource\Db\Mysql\Sql\Select' .
					 '\MysqlSelectBuilderInterface';
		$this->assertInstanceof($interface, $builder);
	}
}
