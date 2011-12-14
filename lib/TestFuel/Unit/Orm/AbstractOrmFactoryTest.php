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
namespace TestFuel\Unit\Orm;

use StdClass,
	Appfuel\Orm,
	TestFuel\TestCase\BaseTestCase;

/**
 * The Abstract Orm Factory supplies the create for a few default objects
 * the don't need to be extended if you are not doing anything special 
 */
class AbstractOrmFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AbstractOrmFactory
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = $this->getMockForAbstractClass(
			'Appfuel\Orm\AbstractOrmFactory'
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->factory);
	}

	public function testDomainInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Orm\OrmFactoryInterface',
			$this->factory
		);
	}
}
