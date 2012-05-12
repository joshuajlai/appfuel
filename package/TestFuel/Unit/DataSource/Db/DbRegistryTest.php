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
	Appfuel\DataSource\Db\DbRegistry,
	Appfuel\DataStructure\Dictionary;

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
	 * @var string
	 */
	protected $dInterface = 'Appfuel\DataStructure\DictionaryInterface';

	/**
	 * @var string
	 */
	protected $cInterface = 'Appfuel\DataSource\Db\DbConnectorInterface';

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->bkParams = DbRegistry::getAllConnectionParams();
		$this->bkConns  = DbRegistry::getAllConnectors();
		DbRegistry::clear();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		if (! empty($this->bkParams)) {
			DbRegistry::setConnectionParams($this->bkParams);
			$this->bkParams = array();
		}
		else {
			DbRegistry::clearConnectionParams();
		}

		if (! empty($this->bkConns)) {
			DbRegistry::setConnectors($this->bkConns);
			$this->bkConns = array();
		}
		else {
			DbRegistry::clearConnectors();
		}
	}

	/**
	 * @return	DictionaryInterface
	 */
	public function getMockDictionary()
	{
		return $this->getMock($this->dInterface);
	}

	/**
	 * @return DbConnectorInterface
	 */
	public function getMockConnector()
	{
		return $this->getMock($this->cInterface);
	}

	/**
	 * @return	null
	 */
	public function testAddGetIsConnectionParamAsAnArray()
	{
		$key = 'local-appfuel-unittest';
		$params = array(
			'name' => 'appfuel_unittest',
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'password',
		);

		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
		$this->assertNull(DbRegistry::addConnectionParams($key, $params));
		$this->assertTrue(DbRegistry::isConnectionParams($key));
	
		$result = DbRegistry::getConnectionParams($key);
		$this->assertInstanceOf('Appfuel\DataStructure\Dictionary', $result);
		$this->assertEquals($params, $result->getAll());

		$expected = array($key => new Dictionary($params));
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());

		$key2 = 'qa-appfuel-unittest';
		$params2 = array(
			'name' => 'appfuel_unittest',
			'host' => 'qa.somedomain.com',
			'user' => 'af_tester',
			'pass' => 'password',
		);
		$this->assertNull(DbRegistry::addConnectionParams($key2, $params2));
		$this->assertTrue(DbRegistry::isConnectionParams($key2));
		
		$result = DbRegistry::getConnectionParams($key2);
		$this->assertInstanceOf('Appfuel\DataStructure\Dictionary', $result);
		$this->assertEquals($params2, $result->getAll());


		$expected = array(
			$key  => new Dictionary($params),
			$key2 => new Dictionary($params2)
		);
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testAddGetIsConnectionParamAsDictionary()
	{
		$key = 'local-appfuel-unittest';
		$params = $this->getMockDictionary();
		
		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
		$this->assertNull(DbRegistry::addConnectionParams($key, $params));
		$this->assertTrue(DbRegistry::isConnectionParams($key));
	
		$result = DbRegistry::getConnectionParams($key);
		$this->assertSame($params, $result);
		$expected = array($key => $params);
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());

		$key2 = 'qa-appfuel-unittest';
		$params2 = $this->getMockDictionary();
		$this->assertNull(DbRegistry::addConnectionParams($key2, $params2));
		$this->assertTrue(DbRegistry::isConnectionParams($key2));
		
		$result = DbRegistry::getConnectionParams($key2);
		$this->assertSame($params2, $result);
		$expected = array(
			$key  => $params,
			$key2 => $params2
		);
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testAddNoDuplicates()
	{
		$key = 'local-appfuel-unittest';
		$params = array(
			'name' => 'appfuel_unittest',
			'host' => 'localhost',
			'user' => 'af_tester',
			'pass' => 'password',
		);

		$this->assertNull(DbRegistry::addConnectionParams($key, $params));
	
		$result = DbRegistry::getConnectionParams($key);
		$this->assertInstanceOf('Appfuel\DataStructure\Dictionary', $result);
		$this->assertEquals($params, $result->getAll());

		$params2 = array(
			'name' => 'appfuel_unittest',
			'host' => 'somedomain.com',
			'user' => 'af_tester',
			'pass' => 'password',
		);
		$this->assertNull(DbRegistry::addConnectionParams($key, $params2));
		$this->assertTrue(DbRegistry::isConnectionParams($key));
		
		$result = DbRegistry::getConnectionParams($key);
		$this->assertInstanceOf('Appfuel\DataStructure\Dictionary', $result);
		$this->assertEquals($params2, $result->getAll());
	}

	/**
	 * @return	null
	 */
	public function testAddEmptyParams()
	{
		$key = 'empty-params';
		$params = array();
		$this->assertNull(DbRegistry::addConnectionParams($key, $params));

		$expected = array($key => new Dictionary());
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());	
	}

	/**
	 * @return null
	 */
	public function testClearConnectionParams()
	{
		$key1 = 'param1';
		$key2 = 'param2';
		$key3 = 'param3';

		$param1 = array('a' => 'value');
		$param2 = array('b' => 'value');
		$param3 = array('c' => 'value');
	
		DbRegistry::addConnectionParams($key1, $param1);	
		DbRegistry::addConnectionParams($key2, $param2);	
		DbRegistry::addConnectionParams($key3, $param3);

		$expected = array(
			$key1 => new Dictionary($param1),
			$key2 => new Dictionary($param2),
			$key3 => new Dictionary($param3),
		);

		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());
		$this->assertNull(DbRegistry::clearConnectionParams());
		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testLoadConnectionParamsNoExistingParams()
	{
		$list = array(
			'param1' => $this->getMockDictionary(),
			'param2' => array('host'=>'myhost', 'user'=>'me', 'pass'=>'pass'),
			'param3' => $this->getMockDictionary(),
		);

		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
		$this->assertNull(DbRegistry::loadConnectionParams($list));

		$expected = array(
			'param1' => $list['param1'],
			'param2' => new Dictionary($list['param2']),
			'param3' => $list['param3']
		);
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testLoadConnectionParamsExistingParams()
	{
		$list1 = array(
			'param1' => $this->getMockDictionary(),
			'param2' => $this->getMockDictionary(),
			'param3' => $this->getMockDictionary(),
		);
		$list2 = array(
			'param4' => $this->getMockDictionary(),
			'param5' => $this->getMockDictionary(),
			'param6' => $this->getMockDictionary(),
		);
		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
		DbRegistry::loadConnectionParams($list1);
		DbRegistry::loadConnectionParams($list2);

		$expected = array_merge($list1, $list2);
		$this->assertEquals($expected, DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testSetConnectionParams()
	{
		$list = array(
			'param1' => $this->getMockDictionary(),
			'param2' => $this->getMockDictionary(),
			'param3' => $this->getMockDictionary(),
		);
		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());

		$this->assertNull(DbRegistry::setConnectionParams($list));
		$this->assertEquals($list, DbRegistry::getAllConnectionParams());

		$list2 = array(
			'param4' => $this->getMockDictionary(),
			'param5' => $this->getMockDictionary(),
			'param6' => $this->getMockDictionary(),
		);
		$this->assertNull(DbRegistry::setConnectionParams($list2));
		$this->assertEquals($list2, DbRegistry::getAllConnectionParams());
	}

	/**
	 * @return	null
	 */
	public function testIsAddGetConnector()
	{
		$key1  = 'my-conn';
		$conn1 = $this->getMockConnector();
		$this->assertEquals(array(), DbRegistry::getAllConnectors());
		$this->assertFalse(DbRegistry::isConnector($key1));
		$this->assertNull(DbRegistry::addConnector($key1, $conn1));
		$this->assertTrue(DbRegistry::isConnector($key1));
		$this->assertSame($conn1, DbRegistry::getConnector($key1));

		$key2  = 'second-conn';
		$conn2 = $this->getMockConnector();
		$this->assertFalse(DbRegistry::isConnector($key2));
		$this->assertNull(DbRegistry::addConnector($key2, $conn2));
		$this->assertTrue(DbRegistry::isConnector($key2));
		$this->assertSame($conn2, DbRegistry::getConnector($key2));

		$expected = array(
			$key1 => $conn1,
			$key2 => $conn2
		);
		$this->assertEquals($expected, DbRegistry::getAllConnectors());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testIsAddConnectorInvalidKeyInt()
	{
		DbRegistry::addConnector(12345, $this->getMockConnector());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testIsAddConnectorInvalidKeyArray()
	{
		DbRegistry::addConnector(array(1,2,3), $this->getMockConnector());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testIsAddConnectorInvalidKeyObject()
	{
		DbRegistry::addConnector(new StdClass(), $this->getMockConnector());
	}

	/**
	 * Load appends to the list and does not override the list
	 * @return	null
	 */
	public function testLoadConnectors()
	{
		$list = array(
			'key1' => $this->getMockConnector(),
			'key2' => $this->getMockConnector(),
			'key3' => $this->getMockConnector()
		);

		$this->assertEquals(array(), DbRegistry::getAllConnectors());
		$this->assertNull(DbRegistry::loadConnectors($list));
		$this->assertTrue(DbRegistry::isConnector('key1'));
		$this->assertSame($list['key1'], DbRegistry::getConnector('key1'));

		$this->assertTrue(DbRegistry::isConnector('key2'));
		$this->assertSame($list['key2'], DbRegistry::getConnector('key2'));

		$this->assertTrue(DbRegistry::isConnector('key3'));
		$this->assertSame($list['key3'], DbRegistry::getConnector('key3'));
		$this->assertEquals($list, DbRegistry::getAllConnectors());

		/* add another list */
		$list2 = array(
			'key4' => $this->getMockConnector(),
			'key5' => $this->getMockConnector(),
		);
		$this->assertNull(DbRegistry::loadConnectors($list2));
		$this->assertTrue(DbRegistry::isConnector('key4'));
		$this->assertSame($list2['key4'], DbRegistry::getConnector('key4'));

		$this->assertTrue(DbRegistry::isConnector('key5'));
		$this->assertSame($list2['key5'], DbRegistry::getConnector('key5'));

		$expected = array_merge($list, $list2);
		$this->assertEquals($expected, DbRegistry::getAllConnectors());
	}

	public function testSetConnectors()
	{
		$list = array(
			'key1' => $this->getMockConnector(),
			'key2' => $this->getMockConnector(),
			'key3' => $this->getMockConnector()
		);
		$this->assertEquals(array(), DbRegistry::getAllConnectors());
		$this->assertNull(DbRegistry::setConnectors($list));
		$this->assertNull(DbRegistry::loadConnectors($list));
		$this->assertTrue(DbRegistry::isConnector('key1'));
		$this->assertSame($list['key1'], DbRegistry::getConnector('key1'));

		$this->assertTrue(DbRegistry::isConnector('key2'));
		$this->assertSame($list['key2'], DbRegistry::getConnector('key2'));

		$this->assertTrue(DbRegistry::isConnector('key3'));
		$this->assertSame($list['key3'], DbRegistry::getConnector('key3'));
		$this->assertEquals($list, DbRegistry::getAllConnectors());

		/* set another list */
		$list2 = array(
			'key4' => $this->getMockConnector(),
			'key5' => $this->getMockConnector(),
		);
		$this->assertNull(DbRegistry::setConnectors($list2));
		$this->assertTrue(DbRegistry::isConnector('key4'));
		$this->assertSame($list2['key4'], DbRegistry::getConnector('key4'));

		$this->assertTrue(DbRegistry::isConnector('key5'));
		$this->assertSame($list2['key5'], DbRegistry::getConnector('key5'));
		$this->assertFalse(DbRegistry::isConnector('key1'));
		$this->assertFalse(DbRegistry::isConnector('key2'));
		$this->assertFalse(DbRegistry::isConnector('key3'));

		$this->assertEquals($list2, DbRegistry::getAllConnectors());
	}

	public function testClearConnectors()
	{
		$list = array(
			'key1' => $this->getMockConnector(),
			'key2' => $this->getMockConnector(),
			'key3' => $this->getMockConnector()
		);
		$this->assertEquals(array(), DbRegistry::getAllConnectors());
		$this->assertNull(DbRegistry::setConnectors($list));
		$this->assertNull(DbRegistry::loadConnectors($list));
		$this->assertNull(DbRegistry::clearConnectors());
		$this->assertFalse(DbRegistry::isConnector('key1'));
		$this->assertFalse(DbRegistry::isConnector('key2'));
		$this->assertFalse(DbRegistry::isConnector('key3'));

		$this->assertEquals(array(), DbRegistry::getAllConnectors());
	}

	public function testClear()
	{
		$list = array(
			'key1' => $this->getMockConnector(),
			'key2' => $this->getMockConnector(),
			'key3' => $this->getMockConnector()
		);
		DbRegistry::setConnectors($list);

		$list2 = array(
			'param1' => $this->getMockDictionary(),
			'param2' => $this->getMockDictionary(),
			'param3' => $this->getMockDictionary(),
		);
		DbRegistry::setConnectionParams($list2);
		
		$this->assertNull(DbRegistry::clear());
		$this->assertEquals(array(), DbRegistry::getAllConnectors());
		$this->assertEquals(array(), DbRegistry::getAllConnectionParams());
	}
}
