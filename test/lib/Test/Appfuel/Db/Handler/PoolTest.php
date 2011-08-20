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
namespace Test\Appfuel\Db\Adapter;
	
use	StdClass,
	Appfuel\Db\Handler\Pool,
	Test\DbCase as ParentTestCase;

/**
 * The database pool holds all the connections and determines which connection
 * to return based on type
 */
class PoolTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Pool
	 */
	protected $pool = null;

	/**
	 * Save the current state of the Pool
	 */
	public function setUp()
	{
		$this->pool = new Pool();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->pool);
	}

	/**
	 * @return	ConnectionInterface
	 */
	public function getMockConnection()
	{
		$class   = 'Appfuel\Framework\Db\Connection\ConnectionInterface';
		$methods = array(
			'close', 
			'connect', 
			'getConnectionDetail',
			'setConnectionDetail'
		);
		return $this->getMockBuilder($class)
					->disableOriginalConstructor()
					->setMethods($methods)
					->getMock();
	}

	/**
	 * @return	null
	 */
	public function testGetSetIsMaster()
	{
		/* default value */
		$this->assertNull($this->pool->getMaster());
		$this->assertFalse($this->pool->isMaster());
		
		$conn = $this->getMockConnection();
		$this->assertSame($this->pool, $this->pool->setMaster($conn));
		$this->assertSame($conn, $this->pool->getMaster());
		$this->assertTrue($this->pool->isMaster());
	}

	/**
	 * @return	null
	 */
	public function testGetSetIsSlave()
	{
		/* default value */
		$this->assertNull($this->pool->getSlave());
		$this->assertFalse($this->pool->isSlave());
		
		$conn = $this->getMockConnection();
		$this->assertSame($this->pool, $this->pool->setSlave($conn));
		$this->assertSame($conn, $this->pool->getSlave());
		$this->assertTrue($this->pool->isSlave());
	}

	/**
	 * When nothing is given addConnection will assign a master
	 * and getConnection will try and retrieve a master
	 *
	 * @return	null
	 */
	public function testAddConnectionValidDefaultType()
	{
		$conn = $this->getMockConnection();
		$this->assertTrue($this->pool->addConnection($conn));
		$this->assertTrue($this->pool->isMaster());
		$this->assertFalse($this->pool->isSlave());
		$this->assertSame($conn, $this->pool->getMaster());
		$this->assertSame($conn, $this->pool->getConnection());
	}

	/**
	 * @return	null
	 */
	public function testAddConnectionValidMaster()
	{
		$conn = $this->getMockConnection();
		$this->assertTrue($this->pool->addConnection($conn, 'master'));
		$this->assertTrue($this->pool->isMaster());
		$this->assertFalse($this->pool->isSlave());
		$this->assertSame($conn, $this->pool->getMaster());
	}

	/**
	 * @return	null
	 */
	public function testAddConnectionValidMasterCase()
	{
		$conn = $this->getMockConnection();
		$this->assertTrue($this->pool->addConnection($conn, 'MASTER'));
		$this->assertTrue($this->pool->isMaster());
		$this->assertFalse($this->pool->isSlave());
		$this->assertSame($conn, $this->pool->getMaster());
	}

	/**
	 * @return	null
	 */
	public function testAddConnectionValidSlave()
	{
		$conn = $this->getMockConnection();
		$this->assertTrue($this->pool->addConnection($conn, 'slave'));
		$this->assertFalse($this->pool->isMaster());
		$this->assertTrue($this->pool->isSlave());
		$this->assertSame($conn, $this->pool->getSlave());
	}

	/**
	 * @return	null
	 */
	public function testAddConnectionValidSlaveCase()
	{
		$conn = $this->getMockConnection();
		$this->assertTrue($this->pool->addConnection($conn, 'SLAVE'));
		$this->assertFalse($this->pool->isMaster());
		$this->assertTrue($this->pool->isSlave());
		$this->assertSame($conn, $this->pool->getSlave());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddConnectionInvalidTypeNotMasterOrSlave()
	{
		$conn = $this->getMockConnection();
		$this->pool->addConnection($conn, 'blah');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddConnectionInvalidTypeEmptyString()
	{
		$conn = $this->getMockConnection();
		$this->pool->addConnection($conn, '');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddConnectionInvalidTypeArray()
	{
		$conn = $this->getMockConnection();
		$this->pool->addConnection($conn, array(12345));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddConnectionInvalidTypeInt()
	{
		$conn = $this->getMockConnection();
		$this->pool->addConnection($conn, 12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return	null
	 */
	public function testAddConnectionInvalidTypeObject()
	{
		$conn = $this->getMockConnection();
		$this->pool->addConnection($conn, new StdClass());
	}

	/**
	 * @return null
	 */
	public function testShutdownBoth()
	{
		$master = $this->getMockConnection();
		$this->pool->setMaster($master);
		$this->assertTrue($this->pool->isMaster());

		$slave = $this->getMockConnection();
		$this->pool->setSlave($slave);
		$this->assertTrue($this->pool->isSlave());

		$this->assertNull($this->pool->shutdown());
		
		$this->assertFalse($this->pool->isMaster());		
		$this->assertFalse($this->pool->isSlave());		
	}

	/**
	 * @return null
	 */
	public function testShutdownMasterNoSlave()
	{
		$master = $this->getMockConnection();
		$this->pool->setMaster($master);
		$this->assertTrue($this->pool->isMaster());
		$this->assertFalse($this->pool->isSlave());
		
		$this->assertNull($this->pool->shutdown());
		
		$this->assertFalse($this->pool->isMaster());		
		$this->assertFalse($this->pool->isSlave());		
	}

	/**
	 * @return null
	 */
	public function testShutdownSlaveNoMaster()
	{
		$slave = $this->getMockConnection();
		$this->pool->setSlave($slave);
		$this->assertTrue($this->pool->isSlave());
		$this->assertFalse($this->pool->isMaster());
		
		$this->assertNull($this->pool->shutdown());
		
		$this->assertFalse($this->pool->isMaster());		
		$this->assertFalse($this->pool->isSlave());		
	}

	/**
	 * @return null
	 */
	public function testShutdownNoMasterOrSlave()
	{
		$this->assertFalse($this->pool->isSlave());
		$this->assertFalse($this->pool->isMaster());
		
		$this->assertNull($this->pool->shutdown());
		
		$this->assertFalse($this->pool->isMaster());		
		$this->assertFalse($this->pool->isSlave());		
	}


}
