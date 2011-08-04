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
	public function provideValidIp_ipv4()
	{
		return array(
			array('192.168.1.1'),
			array('72.215.140.69'),
			array('255.0.0.0'),
			array('10.0.0.1'),
			array('127.0.0.1'),
			array('0.0.0.0'),
			array('1.1.1.1')
		);
	}

	/**
	 * @return	array
	 */
	public function provideFalseIp()
	{
		return array(
			array(''),
			array(1234),
			array('1.2.3.4.5'),
			array('....'),
		);
	}

	/**
	 * @depends			testInterfaces
	 * @dataProvider	provideValidIp_ipv4
	 * @return			null
	 */
	public function testFilterValidIp($raw)
	{
		$params = new Dictionary();
		$result = $this->filter->filter($raw, $params);
		$this->assertEquals($raw, $result);
	}
}
