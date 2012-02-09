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
	Appfuel\Kernel\Mvc\MvcRouteDetail,
	Appfuel\Kernel\Mvc\MvcRouteHandler;

/**
 */
class MvcRouteHandlerTest extends BaseTestCase
{
	protected $masterKey = null;

	/**
	 * System under test
	 * @var MvcRouteHandler
	 */
	protected $handler = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->masterKey = 'master-key';
		$this->handler = new MvcRouteHandler($this->masterKey);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->handler = null;
	}

	/**
	 * @return	array
	 */
	public function getMasterConfig()
	{
		return array(
			'is-public'		=> false,
			'is-internal'	=> true,
			'acl-access'	=> array('admin', 'manager', 'editor'),
			'intercept'		=> array(
				'include-pre'	=> array('my_filter'),
				'exclude-pre'	=> array('your_filter'),
				'is-skip-post'	=> true,
			),
			'view-detail' => array(
				'is-view'  => false,
				'strategy' => 'my-strategy',
				'params'   => array('a', 'b'),
				'method'   => 'my_method'
			),
		);
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$key = $this->masterKey;
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface',
			$this->handler
		);

		$this->assertEquals($key, $this->handler->getMasterKey());
		
		$detail = $this->handler->getMasterDetail();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetail',
			$detail
		);

		$this->assertTrue($this->handler->isValidKey($key));
		$this->assertSame($detail, $this->handler->getRouteDetail($key));
	}

	/**
	 * @return	null
	 */
	public function testLoadMasterConfig()
	{
		$key = $this->masterKey;
		$data = $this->getMasterConfig();

		$handler = new MvcRouteHandler($this->masterKey, $data);
		$this->assertTrue($this->handler->isValidKey($key));
	
		$detail = $handler->getMasterDetail();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetail',
			$detail
		);

		$this->assertFalse($detail->isPublicAccess());
		$this->assertTrue($detail->isInternalOnlyAccess());
		$this->assertTrue($detail->isAccessAllowed('admin'));
		$this->assertTrue($detail->isAccessAllowed('manager'));
		$this->assertTrue($detail->isAccessAllowed('editor'));
		$this->assertTrue($detail->isPreFilters());
		$this->assertTrue($detail->isExcludedPreFilters());
		$this->assertFalse($detail->isSkipPreFilters());
		$this->assertTrue($detail->isSkipPostFilters());
		$this->assertEquals(array('my_filter'), $detail->getPreFilters());
		$this->assertEquals(
			array('your_filter'), 
			$detail->getExcludedPreFilters()
		);
		$this->assertFalse($detail->isPostFilters());
		$this->assertEquals(array(), $detail->getPostFilters());

		$viewDetail = $detail->getViewDetail();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcViewDetail',
			$viewDetail
		);
		$this->assertFalse($viewDetail->isView());
		$this->assertEquals('my-strategy', $viewDetail->getStrategy());
		$this->assertEquals(array('a','b'), $viewDetail->getParams());
		$this->assertEquals('my_method', $viewDetail->getMethod());

		$this->assertSame($detail, $handler->getRouteDetail($key));
	}

	public function testMasterWithAliasThatPointsToMaster()
	{
		$key  = $this->masterKey;
		$data =  array(
			'is-public'		=> false,
			'is-internal'	=> true,
			'acl-access'	=> array('admin', 'manager', 'editor'),
			'intercept'		=> array(
				'include-pre'	=> array('my_filter'),
				'exclude-pre'	=> array('your_filter'),
				'is-skip-post'	=> true,
			),
			'view-detail' => array(
				'is-view'  => false,
				'strategy' => 'my-strategy',
				'params'   => array('a', 'b'),
				'method'   => 'my_method'
			),
		);

		$aliases = array('alias-a' => false);

		$handler = new MvcRouteHandler($key, $data, $aliases);
		$this->assertTrue($handler->isValidKey('alias-a'));
		
		$detail = $handler->getMasterDetail();
		
		/* alias a points to master detail */
		$this->assertSame($detail, $handler->getRouteDetail('alias-a'));
	}

	public function testAliasPointToAnotherAlias()
	{
		$key = 'my-key';
		$aliases = array(
			'alias-b' => array(
				'is-inherit'  => false,
				'is-internal' => true,
			),
			'alias-c' => 'alias-b'
		);

		$handler = new MvcRouteHandler($key, array(), $aliases);
		$this->assertTrue($handler->isValidKey('alias-b'));
		$this->assertTrue($handler->isValidKey('alias-c'));
		
		$detail = $handler->getRouteDetail('alias-b');
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetail',
			$detail
		);
		$this->assertTrue($detail->isInternalOnlyAccess());
		$this->assertSame($detail, $handler->getRouteDetail('alias-c'));
	}
}
