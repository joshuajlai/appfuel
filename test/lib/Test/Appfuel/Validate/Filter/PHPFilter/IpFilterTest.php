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
	Test\AfTestCase as ParentTestCase,
	Appfuel\Validate\Filter\PHPFilter\IpFilter,
	Appfuel\Framework\DataStructure\Dictionary;

/**
 * Test ip filter
 */
class IpFilterTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var IpFilter
	 */
	protected $filter = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->filter = new IpFilter();
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

		$this->assertInstanceOf(
			'Appfuel\Validate\Filter\ValidateFilter',
			$this->filter
		);
	}

	/**
	 * @return	array
	 */
	public function provideTrueValues()
	{
		return array(
			array(true),
			array(1),
			array('1'),
			array('yes'),
			array('on'),
			array('true')
		);
	}

	/**
	 * @return	array
	 */
	public function provideFalseValues()
	{
		return array(
			array('false'),
			array('0'),
			array('false'),
			array('off'),
		);
	}

}
