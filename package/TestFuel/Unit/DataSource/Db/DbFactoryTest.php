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
	Appfuel\DataSource\Db\DbFactory,
	Appfuel\DataStructure\Dictionary;

/**
 * Test the ability of the registry to add, load, set and clear connection
 * parameters and db connectors
 */
class DbFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var DbFactory
	 */
	protected $factory = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->factory = new DbFactory();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->factory = null;
	}

	/**
	 * @return	DbFactory
	 */
	public function getFactory()
	{
		return $this->factory;
	}

	/**
	 * @return	DictionaryInterface
	 */
	public function getMockDictionary()
	{
		return $this->getMock('Appfuel\DataStructure\DictionaryInterface');
	}
	
	/**
	 * @return	null
	 */
	public function testCreateConnection()
	{
		$factory = $this->getFactory();
		$class   = 'Appfuel\DataSource\Db\Mysql\Mysqli\MysqliConn';
		$result  = $factory->createConnection($class, array());
		$this->assertInstanceOf($class, $result);

		/* we are not connecting so it does not need to be real connection
		 * paramaters
		 */
		$params = array(
			'host' => 'localhost',
			'user' => 'myuser',
			'pass' => 'mypass',
			'name' => 'dbname'
		);
		$result  = $factory->createConnection($class, $params);
		$this->assertInstanceOf($class, $result);

		$params = $this->getMockDictionary();
		$result  = $factory->createConnection($class, $params);
		$this->assertInstanceOf($class, $result);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testCreateConnectionInvalidInterface()
	{
		$factory = $this->getFactory();
		$class   = 'StdClass';
		$result  = $factory->createConnection($class, array());
	}

	/**
	 * @return	null
	 */
	public function testCreateConnectorWithNoClassMasterNoSlave()
	{
		$factory = $this->getFactory();
		
		$connInterface = 'Appfuel\DataSource\Db\DbConnInterface';
		$master = $this->getMock($connInterface);
		$result = $factory->createConnector($master);
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\DbConnector',
			$result
		);
		$this->assertSame($master, $result->getMaster());
	}

	/**
	 * @return	null
	 */
	public function testCreateConnectorWithNoClassMasterAndSlave()
	{
		$factory = $this->getFactory();
		
		$connInterface = 'Appfuel\DataSource\Db\DbConnInterface';
		$master = $this->getMock($connInterface);
		$slave  = $this->getMock($connInterface);
		$result = $factory->createConnector($master, $slave);
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\DbConnector',
			$result
		);
		$this->assertSame($master, $result->getMaster());
		$this->assertSame($slave, $result->getSlave());
	}

	/**
	 * @return	null
	 */
	public function testCreateConnectorClassMasterAndSlave()
	{
		$factory = $this->getFactory();
	
		$class = 'Appfuel\DataSource\Db\DbConnector';	
		$connInterface = 'Appfuel\DataSource\Db\DbConnInterface';
		$master = $this->getMock($connInterface);
		$slave  = $this->getMock($connInterface);
		$result = $factory->createConnector($master, $slave, $class);
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\DbConnector',
			$result
		);
		$this->assertSame($master, $result->getMaster());
		$this->assertSame($slave, $result->getSlave());
	}

	/**
	 * @return	null
	 */
	public function testCreateConnectorClassMasterNoSlave()
	{
		$factory = $this->getFactory();
	
		$class = 'Appfuel\DataSource\Db\DbConnector';	
		$connInterface = 'Appfuel\DataSource\Db\DbConnInterface';
		$master = $this->getMock($connInterface);
		$result = $factory->createConnector($master, null, $class);
		$this->assertInstanceOf(
			'Appfuel\DataSource\Db\DbConnector',
			$result
		);
		$this->assertSame($master, $result->getMaster());
		$this->assertNull($result->getSlave());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testCreateConnectorClassDoesNotImplementInterface()
	{
		$factory = $this->getFactory();
		$class = 'StdClass';	
		$connInterface = 'Appfuel\DataSource\Db\DbConnInterface';
		$master = $this->getMock($connInterface);
		$result = $factory->createConnector($master, null, $class);
	}
}
