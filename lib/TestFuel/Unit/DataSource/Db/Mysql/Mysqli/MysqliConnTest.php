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
namespace TestFuel\Unit\DataSource\Db\Mysql\Mysqli;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\DbRegistry,
	Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn;

/**
 */
class MysqliConnTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MysqliConn
	 */
	protected $conn = null;

	/**
	 * Parameters used to connect to the database
	 * @var array
	 */
	protected $params = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->runDbStartupTask();
		$this->params = DbRegistry::getConnectionParams('af-tester');
		$this->conn = new MysqliConn($this->params);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->params = null;
		$this->conn   = null;
	}

	/**	
	 * @return	MysqliConnInterface
	 */
	public function getMysqliConnector()
	{
		return $this->conn;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$conn = $this->getMysqliConnector();
		$this->assertInstanceof('Appfuel\DataSource\Db\DbConnInterface',$conn);
		$this->assertInstanceof(
			'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConnInterface',
			$conn
		);
	}

}
