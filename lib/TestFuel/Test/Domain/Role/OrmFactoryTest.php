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
namespace TestFuel\Test\Domain\Role;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Role\OrmFactory;

/**
 * Test the factory's ability to create the source handler
 */
class OrmFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var OrmFactory
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = new OrmFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->factory);
	}

	/**
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\OrmFactoryInterface',
			$this->factory
		);
	}

	/**
	 * @return null
	 */
	public function testCreateSourceHandler()
	{
		$this->assertInstanceOf(
			'Appfuel\Domain\Role\SourceHandler',
			$this->factory->createSourceHandler()
		);
	}
}
