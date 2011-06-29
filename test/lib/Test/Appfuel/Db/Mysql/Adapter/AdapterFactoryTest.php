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

use Test\AfTestCase as ParentTestCase,
	Appfuel\Db\Mysql\Adapter\AdapterFactory;

/**
 * Test the factories ability to create MysqliAdapter objects
 */
class AdapterFactoryTest extends ParentTestCase
{
	/**
	 * @return null
	 */
	public function testCreateAdapter()
	{
		$conn = $this->getMock(
			'Appfuel\Framework\Db\Connection\ConnectionDetailInterface'
		);

		$result = AdapterFactory::createAdapter($conn);
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Adapter\AdapterInterface',
			$result
		);
		$this->assertInstanceOf(
			'Appfuel\Db\Mysql\Adapter\MysqliAdapter',
			$result
		);
	}
}
