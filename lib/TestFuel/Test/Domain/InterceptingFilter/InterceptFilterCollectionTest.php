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
namespace TestFuel\Test\Domain\Action;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\InterceptFilter\InterceptFilterDomain,
	Appfuel\Domain\InterceptFilter\InterceptFilterCollection;

/**
 * Test the action domain describes the action controller
 */
class InterceptFilterCollectionTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var InterceptFilterCollection
	 */
	protected $collection = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->collection = new InterceptFilterCollection();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->collection = null;
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$data = array(
			array(
				'id'		  => 99,
				'key'		  => 'filterKeyA',
				'type'		  => 'pre',
				'description' => 'this is pre filter'
			),
			array(
				'id'		  => 55,
				'key'		  => 'filterKeyB',
				'type'		  => 'post',
				'description' => 'this is post filter'
			),
		);
					

		return array($data);
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainCollectionInterface',
			$this->collection
		);

		$builder = $this->collection->getDomainBuilder();
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainBuilderInterface',
			$builder
		);
	}

	/**
	 * This class has a fixed domain key that can not be changed
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDomainKey()
	{
		$this->assertEquals('af-intercept', $this->collection->getDomainKey());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddValid()
	{
		$filter = new InterceptFilterDomain();
		$this->assertSame(
			$this->collection,
			$this->collection->add($filter),
			'uses fluent interface'
		);
		$this->assertTrue($this->collection->valid());
		$this->assertSame($filter, $this->collection->current());
	}
}
