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
namespace Test\Appfuel\Validate\Filter;

use StdClass,
	Test\AfTestCase as ParentTestCase;

/**
 * Test the controller's ability to add rules or filters to fields and 
 * validate or sanitize those fields
 */
class ValidateFilterTest extends ParentTestCase
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
		$this->filter = $this->getMockForAbstractClass(
			'Appfuel\Validate\Filter\ValidateFilter'
		);
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
			'Appfuel\Framework\Validate\Filter\FilterInterface',
			$this->filter
		);
	}

	/**
	 * Token string failure 
	 */
	public function testFailedFilterToken()
	{
		$this->assertEquals(
			'__AF_VALIDATE_FILTER_FAILURE__',
			$this->filter->failedFilterToken(),
			'key used that is likely not to be a value'
		);
	}
}
