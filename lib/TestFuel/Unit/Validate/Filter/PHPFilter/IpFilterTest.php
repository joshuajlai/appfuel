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
namespace TestFuel\Test\Validate\Filter\PHPFilter;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Validate\Filter\PHPFilter\IpFilter;

/**
 * Test ip filter
 */
class IpFilterTest extends BaseTestCase
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
		$this->filter = new IpFilter('ip-filter');
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
			array('172.88.1.1'),
			array('72.215.140.69'),
			array('255.0.0.0'),
			array('128.0.0.1'),
			array('127.0.0.1'),
			array('0.0.0.0'),
			array('1.1.1.1')
		);

	}

	/**
	 * @return array
	 */
	public function provideValidIp_privateRange()
	{
		return array(
			array('192.168.0.0'),
			array('192.168.55.123'),
			array('192.168.255.255'),
			array('172.16.0.0'),
			array('172.16.128.128'),
			array('172.31.255.255'),
			array('10.0.0.0'),
			array('10.255.255.255')
		);
	}

	/**
	 * @return array
	 */
	public function provideValidIp_reservedRange()
	{
		return array(
			array('224.0.0.0'),
			array('224.128.128.128'),
			array('224.255.255.255'),
			array('245.0.0.0'),
			array('249.128.128.128'),
			array('250.31.255.255'),
			array('253.255.255.255'),
			array('254.0.0.0')
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidIp()
	{
		return array(
			array(''),
			array(1234),
			array('1.2.3.4.5'),
			array('....'),
			array(array(1,2,3,4)),
			array(new StdClass()),
			array('1000.0.0.0'),
			array('0.1000.0.0'),
			array('0.0.1000.0'),
			array('0.0.0.1000'),
			array('256.255.255.255'),
			array('255.256.255.255'),
			array('255.255.256.255'),
			array('255.255.255.256'),
			array('0.')
		);
	}

	/**
	 * @return array
	 */
	public function provideValidIp_ipv6()
	{
		return array(
			array('10FB:0:0:0:C:ABC:1F0C:44DA'),
			array('10FB::C:ABC:1F0C:44DA'),
			array('FD01:0:0:0:0:0:0:1F'),
			array('FD01::1F'),
			array('0:0:0:0:0:0:0:1'),
			array('::1'),
			array('0:0:0:0:0:0:0:0'),
			array('::')
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
		$this->assertFalse($this->filter->isFailure());
	}

	/**
	 * @depends			testInterfaces
	 * @dataProvider	provideInvalidIp
	 * @return			null
	 */
	public function testFilterValidBadIp($raw)
	{
		$params = new Dictionary();
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * Ipv6 values will fail when you specify ipv4 directly
	 *
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_ipv6
	 * @return			null
	 */
	public function testFilterValidIp4_noIpv6($raw)
	{
		$params = new Dictionary(array('ipv4' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * Ipv4 values will fail when you specify ipv6 directly
	 *
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_ipv4
	 * @return			null
	 */
	public function testFilterValidIp6_noIpv4($raw)
	{
		$params = new Dictionary(array('ipv6' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_ipv6
	 * @return			null
	 */
	public function testFilterValidIp_ipv6($raw)
	{
		$params = new Dictionary(array('ipv6' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);
	}

	/**
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_privateRange
	 * @return			null
	 */
	public function testFilterValidIp_noPrivateRange($raw)
	{
		$params = new Dictionary(array('no-private-ranges' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}

	/**
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_privateRange
	 * @return			null
	 */
	public function testFilterValidIp_allowsPrivateRangesIpv4Only($raw)
	{
		$params = new Dictionary(array('ipv4' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);
	}

	/**
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_reservedRange
	 * @return			null
	 */
	public function testFilterValidIp_allowsReservedRangesIpv4Only($raw)
	{
		$params = new Dictionary(array('ipv4' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertFalse($this->filter->isFailure());
		$this->assertEquals($raw, $result);
	}

	/**
	 * @depends			testInterfaces	
	 * @dataProvider	provideValidIp_reservedRange
	 * @return			null
	 */
	public function testFilterValidIp_noReservedRange($raw)
	{
		$params = new Dictionary(array('no-reserved-ranges' => true));
		$result = $this->filter->filter($raw, $params);
		$this->assertTrue($this->filter->isFailure());
		$this->assertNull($result);
	}
}
