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

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm;

/**
 * The Abstract Orm Factory supplies the create for a few default objects
 * the don't need to be extended if you are not doing anything special 
 */
class AbstractOrmFactoryTest extends ParentTestCase
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
			'Appfuel\Framework\Orm\OrmFactoryInterface',
			$this->factory
		);
	}

	/**
	 * The object factory is used by the domains databuilder and controller
	 * via the repositorys assembler.
	 *
	 * @return Appfuel\Orm\Domain\ObjectFactory
	 */
	public function testCreateObjectFactory()
	{
		$objectFactory = $this->factory->createObjectFactory();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\ObjectFactoryInterface',
			$objectFactory
		);

		return $objectFactory;
	}

	/**
	 * Databuilder is used to convert data comming from a given source into
	 * domain model or datasets.
	 *
	 * @depends		testCreateObjectFactory
	 * @return null
	 */
	public function testCreateDataBuilder($objectFactory)
	{
		$builder = $this->factory->createDataBuilder();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DataBuilderInterface',
			$builder
		);

		$this->assertEquals($objectFactory, $builder->getObjectFactory());
	}

	/**
	 * @return	null
	 */
	public function testCreateDbHandler()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Db\Handler\HandlerInterface',
			$this->factory->createDbHandler()
		);
	}
}
