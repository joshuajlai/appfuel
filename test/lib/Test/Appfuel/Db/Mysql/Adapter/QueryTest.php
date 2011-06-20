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
namespace Test\Appfuel\Db\Mysql\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Connection\ConnectionDetail,
	Appfuel\Db\Mysql\Adapter\Server,
	Appfuel\Db\Mysql\Adapter\Query,
	Mysqli;

/**
 * This class holds only the functionality to perform and debug queries
 */
class AdapterQueryTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Server
	 */
	protected $query = null;

	/**
	 * Hold the connection details
	 * @var ConnectionDetail
	 */
	protected $connDetail = null;
	
	/**
	 * Object responsible for opening and closing the connection
	 * @var Server
	 */
	protected $server = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->connDetail = new ConnectionDetail('mysql', 'mysqli');
		$this->connDetail->setHost('localhost')
						 ->setUserName('appfuel_user')
						 ->setPassword('w3b_g33k')
						 ->setDbName('af_unittest');

		$this->server = new Server($this->connDetail);
		$this->server->initialize();
		$this->server->connect();

		$this->query = new Query($this->server->getHandle());
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->server->close();
		unset($this->connDetail);
		unset($this->query);
		unset($this->server);
	}

	/**
	 * The handle is made immutable by passing it through the constructor
	 * and having no public setter.
	 *
	 * @return	null
	 */
	public function testGetHandle()
	{
		$this->assertInstanceOf('Mysqli', $this->query->getHandle());
	}

}
