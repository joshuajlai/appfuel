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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Appfuel\Kernel\Mvc\RouteView,
	Testfuel\TestCase\BaseTestCase;

class RouteViewTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidString()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function createRouteView()
	{
		$view = new RouteView();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteViewInterface',
			$view
		);

		return $view;
	}

	/**
	 * @test
	 * @depends	createRouteView
	 * @return	RouteIntercept
	 */
	public function format(RouteView $view)
	{
		$this->assertEquals('html', $view->getFormat());
		
		$format = 'json';
		$this->assertSame($view, $view->setFormat($format));
		$this->assertEquals($format, $view->getFormat());

		$format = 'text';
		$this->assertSame($view, $view->setFormat($format));
		$this->assertEquals($format, $view->getFormat());

		$format = 'csv';
		$this->assertSame($view, $view->setFormat($format));
		$this->assertEquals($format, $view->getFormat());

		$format = '';
		$this->assertSame($view, $view->setFormat($format));
		$this->assertEquals($format, $view->getFormat());

		$view->setFormat('html');
		return $view;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidString
	 * @return			null
	 */
	public function setFormatFailure($format)
	{
		$msg = 'route format must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$view = $this->createRouteView();
		$view->setFormat($format);
	}

	/**
	 * @test
	 * @depends	createRouteView
	 * @return	RouteView
	 */
	public function isViewDisabled(RouteView $view)
	{
		$this->assertFalse($view->isViewDisabled());
		$this->assertSame($view, $view->disableView());
		$this->assertTrue($view->isViewDisabled());
	
		$this->assertSame($view, $view->enableView());
		$this->assertFalse($view->isViewDisabled());
		
		return $view;
	}

	/**
	 * @test
	 * @depends	createRouteView
	 * @return	RouteView
	 */
	public function isManualView(RouteView $view)
	{
		$this->assertFalse($view->isManualView());
		$this->assertSame($view, $view->enableManualView());
		$this->assertTrue($view->isManualView());
	
		$this->assertSame($view, $view->disableManualView());
		$this->assertFalse($view->isManualView());
		
		return $view;
	}

	/**
	 * @test
	 * @depends	createRouteView
	 * @return	RouteView
	 */
	public function viewPkg(RouteView $view)
	{
		$this->assertFalse($view->isViewPackage());
		$this->assertNull($view->getViewPackage());

		$pkg = 'appfuel:page.my-page';
		$this->assertSame($view, $view->setViewPackage($pkg));
		$this->assertEquals($pkg, $view->getViewPackage());
		$this->assertTrue($view->isViewPackage());

		$this->assertSame($view, $view->clearViewPackage());
		$this->assertFalse($view->isViewPackage());
		$this->assertNull($view->getViewPackage());

		return $view;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidString
	 * @return			null
	 */
	public function setViewPackageFailure($pkg)
	{
		$msg = 'package name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$view = $this->createRouteView();
		$view->setViewPackage($pkg);
	}

	/**
	 * @test
	 * @depends		createRouteView
	 * @return		null
	 */
	public function setViewPackageEmptyStringFailure(RouteView $view)
	{
		$msg = 'package name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$view->setViewPackage('');
	}


}
