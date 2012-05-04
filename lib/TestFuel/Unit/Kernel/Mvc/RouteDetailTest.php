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
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcRouteDetail;

class RouteDetailTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(true),
			array(false),
			array(12345),
			array(0),
			array(1),
			array(-1),
			array(1.234),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @param	array	$data
	 * @return	MvcRouteDatail
	 */
	public function createRouteDetail(array $data)
	{
		return new MvcRouteDetail($data);
	}

	/**
	 * @test
	 * @return RouteAccess
	 */
	public function createBasicRouteDetail()
	{
		$name   = 'MyAction';
		$detail = $this->createRouteDetail(array('action-name' => $name));
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetailInterface',
			$detail
		);
		/* route startup */
		$this->assertFalse($detail->isIgnoreConfigStartupTasks());
		$this->assertFalse($detail->isPrependStartupTasks());
		$this->assertFalse($detail->isStartupDisabled());
		$this->assertFalse($detail->isStartupTasks());
		$this->assertEquals(array(), $detail->getStartupTasks());
		$this->assertFalse($detail->isExcludedStartupTasks());
		$this->assertEquals(array(), $detail->getExcludedStartupTasks());
	
		/* route intercept */
		$this->assertTrue($detail->isPreFilteringEnabled());
		$this->assertFalse($detail->isPreFilters());
		$this->assertEquals(array(), $detail->getPreFilters());
		$this->assertFalse($detail->isExcludedPreFilters());
		$this->assertEquals(array(), $detail->getExcludedPreFilters());
		$this->assertTrue($detail->isPostFilteringEnabled());
		$this->assertFalse($detail->isPostFilters());
		$this->assertEquals(array(), $detail->getPostFilters());
		$this->assertFalse($detail->isExcludedPostFilters());
		$this->assertEquals(array(), $detail->getExcludedPostFilters());
			
		/* route access */
		$this->assertFalse($detail->isPublicAccess());
		$this->assertFalse($detail->isInternalOnlyAccess());
		$this->assertFalse($detail->isAclAccessIgnored());
		$this->assertFalse($detail->isAccessAllowed('anycode'));

		/* route view */
		$this->assertFalse($detail->isViewDisabled());
		$this->assertFalse($detail->isManualView());
		$this->assertFalse($detail->isViewPackage());
		$this->assertEquals(null, $detail->getViewPackage());
		$this->assertEquals('html', $detail->getFormat());

		/* route action */
		$this->assertEquals($name, $detail->getActionName());
		$this->assertEquals($name, $detail->findActionName());
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeDetailStartup()
	{
		$spec = array(
			'startup' => array(
				'is-prepended'		=> true,
				'is-config-ignored' => true,
				'is-disabled'		=> true,
				'tasks'				=> array('TaskA', 'TaskB'),
				'excluded-tasks'	=> array('TaskC', 'TaskD'),
			),
			'action-name' => 'MyAction',
		);
		$detail = $this->createRouteDetail($spec);

		$this->assertTrue($detail->isPrependStartupTasks());
		$this->assertTrue($detail->isIgnoreConfigStartupTasks());
		$this->assertTrue($detail->isStartupDisabled());
		$this->assertTrue($detail->isStartupTasks());
		$this->assertEquals(
			$spec['startup']['tasks'], 
			$detail->getStartupTasks()
		);
		$this->assertTrue($detail->isExcludedStartupTasks());
		$this->assertEquals(
			$spec['startup']['excluded-tasks'], 
			$detail->getExcludedStartupTasks()
		);
			
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeDetailIntercept()
	{
		$spec = array(
			'intercept' => array(
				'is-skip-pre'	=> true,
				'include-pre'	=> array('PreFilterA', 'PreFilterB'),
				'exclude-pre'	=> array('PreFilterC', 'PreFilterD'),
				'is-skip-post'	=> true,
				'include-post'  => array('PostFilterA', 'PostFilterB'),
				'exclude-post'  => array('PostFilterC', 'PostFilterD')
			),

			'action-name' => 'MyAction',
		);
		$detail = $this->createRouteDetail($spec);

		/* route intercept */
		$this->assertFalse($detail->isPreFilteringEnabled());
		$this->assertTrue($detail->isPreFilters());
		$this->assertEquals(
			$spec['intercept']['include-pre'], 
			$detail->getPreFilters()
		);

		$this->assertTrue($detail->isExcludedPreFilters());
		$this->assertEquals(
			$spec['intercept']['exclude-pre'], 
			$detail->getExcludedPreFilters()
		);
		$this->assertFalse($detail->isPostFilteringEnabled());
		$this->assertTrue($detail->isPostFilters());
		$this->assertEquals(
			$spec['intercept']['include-post'], 
			$detail->getPostFilters()
		);
		$this->assertTrue($detail->isExcludedPostFilters());
		$this->assertEquals(
			$spec['intercept']['exclude-post'], 
			$detail->getExcludedPostFilters()
		);	
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeDetailAccessMapped()
	{
		$spec = array(
			'access' => array(
				'is-public'		=> true,
				'is-internal'	=> true,
				'is-ignore'		=> true,
				'acl-access'    => array(
					'get'   => array('admin', 'editor', 'guest'),
					'put'	=> array('admin', 'editor'),
					'post'	=> array('admin', 'editor'),
					'delete'=> array('admin')
				),
			),
			'action-name' => 'MyAction',
		);
		$detail = $this->createRouteDetail($spec);

		/* route access */
		$this->assertTrue($detail->isPublicAccess());
		$this->assertTrue($detail->isInternalOnlyAccess());
		$this->assertTrue($detail->isAclAccessIgnored());
		
		$result = $detail->isAccessAllowed(
			$spec['access']['acl-access']['get'], 
			'get'
		);
		$this->assertTrue($result);

		$result = $detail->isAccessAllowed(
			$spec['access']['acl-access']['put'], 
			'put'
		);
		$this->assertTrue($result);

		$result = $detail->isAccessAllowed(
			$spec['access']['acl-access']['post'], 
			'post'
		);
		$this->assertTrue($result);


		$result = $detail->isAccessAllowed('admin', 'delete');
		$this->assertTrue($result);

	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeDetailAccessNotMapped()
	{
		$spec = array(
			'access' => array(
				'is-public'		=> false,
				'is-internal'	=> false,
				'is-ignore'		=> false,
				'acl-access'    => array('admin', 'publisher', 'editor'),
			),
			'action-name' => 'MyAction',
		);
		$detail = $this->createRouteDetail($spec);

		/* route access */
		$this->assertFalse($detail->isPublicAccess());
		$this->assertFalse($detail->isInternalOnlyAccess());
		$this->assertFalse($detail->isAclAccessIgnored());
		
		$result = $detail->isAccessAllowed($spec['access']['acl-access']);
		$this->assertTrue($result);
		$this->assertTrue($detail->isAccessAllowed('admin'));
		$this->assertTrue($detail->isAccessAllowed('publisher'));
		$this->assertTrue($detail->isAccessAllowed('editor'));
		$this->assertFalse($detail->isAccessAllowed('guest'));
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeDetailAccessBackwardsCompatibleNotMapped()
	{
		$spec = array(
			'is-public'		=> false,
			'is-internal'	=> false,
			'is-ignore'		=> false,
			'acl-access'    => array('admin', 'publisher', 'editor'),
			'action-name' => 'MyAction',
		);
		$detail = $this->createRouteDetail($spec);

		/* route access */
		$this->assertFalse($detail->isPublicAccess());
		$this->assertFalse($detail->isInternalOnlyAccess());
		$this->assertFalse($detail->isAclAccessIgnored());
		
		$result = $detail->isAccessAllowed($spec['acl-access']);
		$this->assertTrue($result);
		$this->assertTrue($detail->isAccessAllowed('admin'));
		$this->assertTrue($detail->isAccessAllowed('publisher'));
		$this->assertTrue($detail->isAccessAllowed('editor'));
		$this->assertFalse($detail->isAccessAllowed('guest'));
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeViewIsView()
	{
		$spec = array(
			'view' => array(
				'is-view'			=> false,
				'view-pkg'			=> 'appfuel:page.my-page',
				'default-format'	=> 'html'
			),
			'action-name' => 'MyAction'
		);
		$detail = $this->createRouteDetail($spec);

		$this->assertTrue($detail->isViewDisabled());
		$this->assertTrue($detail->isViewPackage());
		$this->assertEquals('appfuel:page.my-page', $detail->getViewPackage());
		$this->assertEquals('html', $detail->getFormat());
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeViewManualView()
	{
		$spec = array(
			'view' => array(
				'is-manual-view'	=> true,
				'view-pkg'			=> 'appfuel:page.my-page',
				'default-format'	=> 'html'
			),
			'action-name' => 'MyAction'
		);
		$detail = $this->createRouteDetail($spec);

		$this->assertTrue($detail->isManualView());
		$this->assertTrue($detail->isViewPackage());
		$this->assertEquals('appfuel:page.my-page', $detail->getViewPackage());
		$this->assertEquals('html', $detail->getFormat());
	}
}
