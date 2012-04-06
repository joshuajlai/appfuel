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
namespace TestFuel\Unit\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Html\Resource\ResourceFactory;

/**
 */
class ResourceTest extends BaseTestCase
{
	/**
	 * @test
	 * @return	Pkg
	 */
	public function createFactory()
	{
		$factory = new ResourceFactory();	
		$this->assertInstanceof(
			'Appfuel\Html\Resource\ResourceFactoryInterface', 
			$factory
		);

		return $factory;
	}

	/**
	 * @test
	 * @depends	createFactory
	 * @return	ResourceFactory
	 */
	public function createResourceAdapter(ResourceFactory $factory)
	{
		$adapter = $factory->createResourceAdapter();
		$this->assertInstanceOf(
			'Appfuel\Html\Resource\AppfuelAdapter',
			$adapter
		);
		$this->assertInstanceOf(
			'Appfuel\Html\Resource\ResourceAdapterInterface',
			$adapter
		);

	}
}
