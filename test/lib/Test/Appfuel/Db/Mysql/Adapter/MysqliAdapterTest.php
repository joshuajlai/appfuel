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
	Appfuel\Db\Mysql\Adapter\MysqliAdapter;

/**
 * Test the adapters ability to wrap mysqli
 */
class MysqliAdapterTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Adapter
	 */
	protected $adapter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
	}

	/**
	 * The connection detail is an immutable object that can only be set in 
	 * the constructor.
	 *
	 * @return	null
	 */
	public function testGetConnectionDetail()
	{
		$this->assertTrue(true);
	}
}
