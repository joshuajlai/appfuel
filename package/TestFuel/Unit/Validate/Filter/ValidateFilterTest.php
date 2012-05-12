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
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class ValidateFilterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ValidateFilter
	 */
	protected $filter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->filterName = 'my-filter';
		$class = 'Appfuel\Validate\Filter\ValidateFilter';
		$this->filter = $this->getMockBuilder($class)
							 ->setConstructorArgs(array($this->filterName))
							 ->getMockForAbstractClass();

	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->filter);
	}

	/**
	 * @return null
	 */
	public function testInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Validate\Filter\FilterInterface',
			$this->filter
		);
	}

	/**
	 * Immutable name is the key used by the factory to create the filter and
	 * is used in the default error message
	 *
	 * @return	null
	 */
	public function testGetName()
	{
		$this->assertEquals($this->filterName, $this->filter->getName());
	}

	public function testGetSetDefaultError()
	{
		$expected = "Filter failure has occured for {$this->filterName}";
		$this->assertEquals($expected, $this->filter->getDefaultError());
	}

	/**
	 * Used to determine if a filter failed. The enableFailure is a protected
	 * method and used internally so we don't need to test it.
	 */
	public function testIsFailure()
	{
		/* default value of a filter is false */
		$this->assertFalse($this->filter->isFailure());
	}
}
