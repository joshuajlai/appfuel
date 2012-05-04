<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RouteIntercept;

class RouteInterceptTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidFilterName()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
			array('')
		);
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function createRouteIntercept()
	{
		$intercept = new RouteIntercept();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteInterceptInterface',
			$intercept
		);

		return $intercept;
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function preFiltering(RouteIntercept $intercept)
	{
		$this->assertTrue($intercept->isPreFilteringEnabled());
		
		$this->assertSame($intercept, $intercept->disablePreFiltering());
		$this->assertFalse($intercept->isPreFilteringEnabled());
		
		$this->assertSame($intercept, $intercept->enablePreFiltering());
		$this->assertTrue($intercept->isPreFilteringEnabled());

		return $intercept;
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function preFilters(RouteIntercept $intercept)
	{
		$filters = array('FilterA', 'FilterB');
		$this->assertFalse($intercept->isPreFilters());
		
		$this->assertSame($intercept, $intercept->setPreFilters($filters));
		$this->assertTrue($intercept->isPreFilters());
		$this->assertEquals($filters, $intercept->getPreFilters());

		$this->assertSame($intercept, $intercept->setPreFilters(array()));
		$this->assertEquals(array(), $intercept->getPreFilters());
		$this->assertFalse($intercept->isPreFilters());
		
		return $intercept;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidFilterName
	 * @return			null
	 */
	public function setPreFiltersFailure($badName)
	{
		$msg = 'pre intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$filters = array('FilterA', $badName);
		$intercept = $this->createRouteIntercept();
		$intercept->setPreFilters($filters);
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function excludedPreFilters(RouteIntercept $intercept)
	{
		$filters = array('FilterA', 'FilterB');
		$this->assertFalse($intercept->isExcludedPreFilters());
		
		$this->assertSame(
			$intercept, 
			$intercept->setExcludedPreFilters($filters)
		);
		$this->assertTrue($intercept->isExcludedPreFilters());
		$this->assertEquals($filters, $intercept->getExcludedPreFilters());

		$this->assertSame(
			$intercept, 
			$intercept->setExcludedPreFilters(array())
		);
		$this->assertEquals(array(), $intercept->getExcludedPreFilters());
		$this->assertFalse($intercept->isExcludedPreFilters());
		
		return $intercept;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidFilterName
	 * @return			null
	 */
	public function setExcludedPreFiltersFailure($badName)
	{
		$msg = 'pre intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$filters = array('FilterA', $badName);
		$intercept = $this->createRouteIntercept();
		$intercept->setExcludedPreFilters($filters);
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function postFiltering(RouteIntercept $intercept)
	{
		$this->assertTrue($intercept->isPostFilteringEnabled());
		
		$this->assertSame($intercept, $intercept->disablePostFiltering());
		$this->assertFalse($intercept->isPostFilteringEnabled());
		
		$this->assertSame($intercept, $intercept->enablePostFiltering());
		$this->assertTrue($intercept->isPostFilteringEnabled());

		return $intercept;
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function postFilters(RouteIntercept $intercept)
	{
		$filters = array('FilterC', 'FilterD');
		$this->assertFalse($intercept->isPostFilters());
		
		$this->assertSame($intercept, $intercept->setPostFilters($filters));
		$this->assertTrue($intercept->isPostFilters());
		$this->assertEquals($filters, $intercept->getPostFilters());

		$this->assertSame($intercept, $intercept->setPostFilters(array()));
		$this->assertEquals(array(), $intercept->getPostFilters());
		$this->assertFalse($intercept->isPostFilters());
		
		return $intercept;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidFilterName
	 * @return			null
	 */
	public function setPostFiltersFailure($badName)
	{
		$msg = 'post intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$filters = array('FilterA', $badName);
		$intercept = $this->createRouteIntercept();
		$intercept->setPostFilters($filters);
	}

	/**
	 * @test
	 * @depends	createRouteIntercept
	 * @return	RouteIntercept
	 */
	public function excludedPostFilters(RouteIntercept $intercept)
	{
		$filters = array('FilterC', 'FilterC');
		$this->assertFalse($intercept->isExcludedPostFilters());
		
		$this->assertSame(
			$intercept, 
			$intercept->setExcludedPostFilters($filters)
		);
		$this->assertTrue($intercept->isExcludedPostFilters());
		$this->assertEquals($filters, $intercept->getExcludedPostFilters());

		$this->assertSame(
			$intercept, 
			$intercept->setExcludedPostFilters(array())
		);
		$this->assertEquals(array(), $intercept->getExcludedPostFilters());
		$this->assertFalse($intercept->isExcludedPostFilters());
		
		return $intercept;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidFilterName
	 * @return			null
	 */
	public function setExcludedPostFiltersFailure($badName)
	{
		$msg = 'post intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$filters = array('FilterA', $badName);
		$intercept = $this->createRouteIntercept();
		$intercept->setExcludedPostFilters($filters);
	}
}
