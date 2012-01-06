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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcRouteDetail;

/**
 * The route detail is a value object used hold info specific to the route
 */
class MvcRouteDetailTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function testInterfaceAndDefaults()
	{
		$key = 'my-route';
		$detail = new MvcRouteDetail($key);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetailInterface',
			$detail
		);
		$this->assertEquals($key, $detail->getRouteKey());
		$this->assertTrue($detail->isPublic());
		$this->assertEquals(array(), $detail->getInterceptingFilters());
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testPrivateRouteDetail()
	{
		$key = 'my-route';
		$isPublic = false;
		$detail = new MvcRouteDetail($key, $isPublic);
		$this->assertEquals($key, $detail->getRouteKey());
		$this->assertFalse($detail->isPublic());
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testPrivateRouteWithAclCodes()
	{
		$key = 'my-route';
		$isPublic = false;
		$codes = array('my-code', 'your-code', 'our-code');
		$detail = new MvcRouteDetail($key, $isPublic, $codes);
		$this->assertEquals($key, $detail->getRouteKey());
		$this->assertFalse($detail->isPublic());
		$this->assertTrue($detail->isAllowed($codes[0]));
		$this->assertTrue($detail->isAllowed($codes[1]));
		$this->assertTrue($detail->isAllowed($codes[2]));
		$this->assertFalse($detail->isAllowed('does-not-exist'));
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testPrivateRouteWithInterceptingFilters()
	{
		$key = 'my-route';
		$isPublic = false;
		$filters = array('Filter\FilterC', 'Filter\FilterB', 'Filter\FilterA');
		$detail = new MvcRouteDetail($key, $isPublic, null, $filters);
		$this->assertEquals($key, $detail->getRouteKey());
		$this->assertFalse($detail->isPublic());
		$this->assertFalse($detail->isAllowed('does-not-exist'));
		$this->assertEquals($filters, $detail->getInterceptingFilters());
	}

	/**
	 * @depends	testInterfaceAndDefaults
	 * @return	null
	 */
	public function testDetailAll()
	{
		$key = 'my-route';
		$isPublic = false;
		$codes = array('my-code', 'your-code', 'our-code');
		$filters = array('Filter\FilterC', 'Filter\FilterB', 'Filter\FilterA');
		$detail = new MvcRouteDetail($key, $isPublic, $codes, $filters);
		$this->assertEquals($key, $detail->getRouteKey());
		$this->assertFalse($detail->isPublic());
		$this->assertTrue($detail->isAllowed($codes[0]));
		$this->assertTrue($detail->isAllowed($codes[1]));
		$this->assertTrue($detail->isAllowed($codes[2]));
		$this->assertFalse($detail->isAllowed('does-not-exist'));
		$this->assertEquals($filters, $detail->getInterceptingFilters());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @depends				testInterfaceAndDefaults
	 * @return				null
	 */
	public function testRouteKeyNotString_Failure($key)
	{
		$detail = new MvcRouteDetail($key);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @depends				testInterfaceAndDefaults
	 * @return				null
	 */
	public function testRouteKeyBadAclCode_Failure($code)
	{
		$codes = array('code1', $code, 'code2');
		$detail = new MvcRouteDetail('my-route', false, $codes);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @depends				testInterfaceAndDefaults
	 * @return				null
	 */
	public function testRouteKeyBadFilter_Failure($filter)
	{
		$filters = array('filter1', $filter, 'fillter2');
		$detail = new MvcRouteDetail('my-route', false, null, $filters);
	}	
}
