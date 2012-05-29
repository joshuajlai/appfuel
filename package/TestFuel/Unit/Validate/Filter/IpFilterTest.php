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
	Appfuel\Validate\Filter\IpFilter;

/**
 * Test ip filter
 */
class IpFilterTest extends FilterBaseTest
{
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
	 * @return	IpFilter
	 */
	public function createFilter()
	{
		return new IpFilter();
	}

	/**
	 * @test
	 * @return null
	 */
	public function filterInterface()
	{
		return parent::filterInterface();
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_ipv4
	 * @return			null
	 */
	public function filterValidIp($raw)
	{
		$filter = $this->createFilter();
		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidIp
	 * @return			null
	 */
	public function filterValidBadIp($raw)
	{
		$filter = $this->createFilter();
		$fail   = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * Ipv6 values will fail when you specify ipv4 directly
	 *
	 * @test
	 * @dataProvider	provideValidIp_ipv6
	 * @return			null
	 */
	public function filterValidIp4_noIpv6($raw)
	{
		$options = $this->createOptions(array('ipv4' => true));
		$filter = $this->createFilter();
		$filter->setOptions($options);
		
		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * Ipv4 values will fail when you specify ipv6 directly
	 *
	 * @test
	 * @dataProvider	provideValidIp_ipv4
	 * @return			null
	 */
	public function estFilterValidIp6_noIpv4($raw)
	{
		$options = $this->createOptions(array('ipv6' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_ipv6
	 * @return			null
	 */
	public function filterValidIp_ipv6($raw)
	{
		$options = $this->createOptions(array('ipv6' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_privateRange
	 * @return			null
	 */
	public function filterValidIp_noPrivateRange($raw)
	{
		$options = $this->createOptions(array('no-private-ranges' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);
		
		$fail = $filter->getFailureToken();
		$this->assertEquals($fail, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_privateRange
	 * @return			null
	 */
	public function filterValidIp_allowsPrivateRangesIpv4Only($raw)
	{
		$options = $this->createOptions(array('ipv4' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_reservedRange
	 * @return			null
	 */
	public function filterValidIp_allowsReservedRangesIpv4Only($raw)
	{
		$options = $this->createOptions(array('ipv4' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$this->assertEquals($raw, $filter->filter($raw));
	}

	/**
	 * @test
	 * @dataProvider	provideValidIp_reservedRange
	 * @return			null
	 */
	public function filterValidIp_noReservedRange($raw)
	{
		$options = $this->createOptions(array('no-reserved-ranges' => true));
		$filter  = $this->createFilter();
		$filter->setOptions($options);

		$fail = $filter->getFailureToken();	
		$this->assertEquals($fail, $filter->filter($raw));
	}
}
