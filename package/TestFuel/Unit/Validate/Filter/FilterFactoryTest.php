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
namespace TestFuel\Unit\Validate\Filter;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Validate\Filter\FilterFactory;

/**
 * Filter factory is responsible for removing the need for knowing the name of
 * the filter class.
 */
class FilterFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterFactory
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = new FilterFactory();
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
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Validate\Filter\FilterFactoryInterface',
			$this->factory
		);
	}

	public function provideFilterMap()
	{
		$ns = 'Appfuel\Validate\Filter\\PHPFilter';
		return array(
			array('php-ip-filter',		"{$ns}\\IpFilter"),
			array('php-float-filter',	"{$ns}\\FloatFilter"),
			array('php-bool-filter',	"{$ns}\\BoolFilter"),
			array('php-email-filter',	"{$ns}\\EmailFilter"),
		);
	}

	/**
	 * Test the ability to create filters by key
	 * 
	 * @dataProvider	provideFilterMap
	 * @return null
	 */
	public function testCreateFilters($key, $class)
	{
		$this->assertInstanceOf($class, $this->factory->createFilter($key));
	}
}
