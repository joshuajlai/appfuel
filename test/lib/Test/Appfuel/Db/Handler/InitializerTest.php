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
namespace Test\Appfuel\Db\Handler;

use Test\DbCase as ParentTestCase,
	Appfuel\Db\Handler\DbInitializer;

/**
 * The initializer is used by appfuel to initialize the correct connections
 * put them into a pool and assign that pool to the correct handler
 */
class DbInitializerTest extends ParentTestCase
{
	protected $initializer = null;

	/**
	 * Save the current state of the Pool
	 */
	public function setUp()
	{
		$this->initializer = new DbInitializer();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->initializer);
	}

	/**
	 * @return null
	 */
	public function testCreateConnectionDetailFactory()
	{
		$result = $this->initializer->createConnectionDetailFactory();
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Connection\DetailFactoryInterface',
			$result
		);

		$this->assertInstanceOf(
			'Appfuel\Db\Connection\DetailFactory',
			$result
		);
	}

	/**
	 * @return null
	 */
	public function testInitializeNoFactory()
	{
		$this->assertTrue(true);
	}


}
