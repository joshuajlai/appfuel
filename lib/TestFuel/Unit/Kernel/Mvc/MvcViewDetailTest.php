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
	Appfuel\Kernel\Mvc\MvcViewDetail;

/**
 * The route detail is a value object used hold info specific to the route
 */
class MvcViewDetailTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function testEmptyDetail()
	{
		$detail = new MvcViewDetail(array());
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcViewDetailInterface',
			$detail
		);

		$this->assertTrue($detail->isView());
		$this->assertEquals('general', $detail->getStrategy());
		$this->assertEquals(array(), $detail->getParams());
		$this->assertNull($detail->getMethod());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testIsView()
	{
		$data = array('is-view' => false);
		$detail = new MvcViewDetail($data);
		$this->assertFalse($detail->isView());
	
		$data = array('is-view' => true);
		$detail = new MvcViewDetail($data);
		$this->assertTrue($detail->isView());
	
		/* only a bool false can make the view false */
		$data = array('is-view' => 0);
		$detail = new MvcViewDetail($data);
		$this->assertTrue($detail->isView());
		
		$data = array('is-view' => 'false');
		$detail = new MvcViewDetail($data);
		$this->assertTrue($detail->isView());
	}

	/**
	 * @dataProvider	provideNonEmptyStringsNoNumbers
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testStrategy($str)
	{
		$data = array('strategy' => $str);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($str, $detail->getStrategy());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testParamsEmptyArray()
	{
		$data = array('params' => array());
		$detail = new MvcViewDetail($data);
		$this->assertEquals(array(), $detail->getParams());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testParams()
	{
		$list = array('a', 'b', 'c');
		$data = array('params' => $list);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($list, $detail->getParams());

		$list = 'abc';
		$data = array('params' => $list);
		$detail = new MvcViewDetail($data);
		$this->assertEquals(array(), $detail->getParams());

		$list = 12345;
		$data = array('params' => $list);
		$detail = new MvcViewDetail($data);
		$this->assertEquals(array(), $detail->getParams());
	}

	/**
	 * @depends		testEmptyDetail
	 * @return		null
	 */
	public function testMethod()
	{
		$method = 'myCustomMethod';
		$data = array('method' => $method);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($method, $detail->getMethod());

		$method = '';
		$data = array('method' => $method);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($method, $detail->getMethod());

		$method = 12345;
		$data = array('method' => $method);
		$detail = new MvcViewDetail($data);
		$this->assertNull($detail->getMethod());

		$method = array(1,2,3,4,5);
		$data = array('method' => $method);
		$detail = new MvcViewDetail($data);
		$this->assertNull($detail->getMethod());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testMethodClosure()
	{
		$myfunc = function($strategy, array $params) {
			return '';
		};
		$data = array('method' => $myfunc);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($myfunc, $detail->getMethod());
	}

	/**
	 * Used to test a callable property
	 *
	 * @param	string	$strategy
	 * @param	array	$params
	 * @return	string
	 */
	public function buildView($strategy, array $params)
	{
		return '';
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testMethodCallback()
	{
		$callback = array($this, 'buildView');
		$data = array('method' => $callback);
		$detail = new MvcViewDetail($data);
		$this->assertEquals($callback, $detail->getMethod());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testAll()
	{
		$data = array(
			'is-view'  => false,
			'strategy' => 'my-strategy',
			'params'   => array('a', 'b', 'c'),
			'method'   => 'my_method'
		);
		$detail = new MvcViewDetail($data);
		$this->assertFalse($detail->isView());
		$this->assertEquals('my-strategy', $detail->getStrategy());
		$this->assertEquals(array('a', 'b', 'c'), $detail->getParams());
		$this->assertEquals('my_method', $detail->getMethod());
	}
}
