<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Validate\Filter;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\Dictionary;

/**
 * Not to be run directly but extends by the filter test classes
 */
class FilterBaseTest extends BaseTestCase
{

	/**
	 * @param	array	$data
	 * @return	Dictionary
	 */
	public function createOptions(array $data)
	{
		return new Dictionary($data);
	}

	/**
	 * @test
	 * @return null
	 */
	public function filterInterface()
	{
		$filter = $this->createFilter();
		$interface = 'Appfuel\Validate\Filter\FilterInterface';
		$parent    = 'Appfuel\Validate\Filter\ValidationFilter';

		$this->assertInstanceOf($interface, $filter);
		$this->assertInstanceOf($parent, $filter);

		return $filter;
	}
}
