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
class InterceptingTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FilterManager
	 */
	protected $filter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$class = 'Appfuel\App\Filter\InterceptingFilter';
		$this->filter	= $this->getMockBuilder($class)
							   ->setConstructorArgs(array('pre'))
							   ->setMethods(array('filter'))
							   ->getMockForAbstractClass();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->filter = null;
	}

	/**
	 * @return	null
	 */
	public function testGetSetType()
	{
		$this->assertEquals('pre', $this->filter->getType());
		$this->assertSame(
			$this->filter,
			$this->filter->setType('post'),
			'uses fluent interface'
		);
		$this->assertEquals('post', $this->filter->getType());

		$this->assertSame(
			$this->filter,
			$this->filter->setType('pre'),
			'uses fluent interface'
		);
		$this->assertEquals('pre', $this->filter->getType());
	}
}
