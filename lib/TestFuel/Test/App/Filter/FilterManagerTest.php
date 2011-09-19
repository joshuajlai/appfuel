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
namespace TestFuel\Test\Filter;

use StdClass,
	Appfuel\App\Filter\FilterManager,
	TestFuel\TestCase\BaseTestCase;

/**
 * Controls the usage for all interceptiong filters
 */
class FilterManagerTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterManager
	 */
	protected $manager = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->manager	= new FilterManager();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->manager = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\App\Filter\FilterManagerInterface',
			$this->manager
		);
	}
}
