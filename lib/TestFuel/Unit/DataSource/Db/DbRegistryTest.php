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
namespace TestFuel\Unit\DataSource\Db;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataSource\Db\DbRegistry;

/**
 * Test the ability of the registry to add, load, set and clear connection
 * parameters and db connectors
 */
class DbRegistryTest extends BaseTestCase
{
	/**
	 * Backup existing connection paramters
	 * @var array
	 */
	protected $bkParams = array();

	/**
	 * Backup existing db connectors
	 * @var array
	 */
	protected $bkConns = array();

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->bkParams = DbRegistry::getAllConnectionParams();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		if (! empty($this->bkParams)) {
			DbRegistry::setConnectionParams($this->bkParams);
		}
		else {
			DbRegistry::clearConnectionParams();
		}
	}

	/**
	 * @return	null
	 */
	public function testAddGetIsConnectionParam()
	{
		$key = 'local-appfuel-unittest';
		$params = array(
			'name' => 'appfuel_unittest',
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'password',
		);

		$this->assertNull(DbRegistry::addConnectionParam($key, $params));
	}
}
