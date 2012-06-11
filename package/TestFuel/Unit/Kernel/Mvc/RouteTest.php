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
	Appfuel\Kernel\Mvc\Route,
	Testfuel\TestCase\BaseTestCase;

class RouteTest extends BaseTestCase
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
	 * @return	MvcRoute
	 */
	public function createRoute(array $data)
	{
		return new Route($data);
	}

	/**
	 * @test
	 * @return RouteAccess
	 */
	public function createBasicRouteDetail()
	{
		$name   = 'MyAction';
		$spec   = array(
			'key'			=> 'my-route',
			'action-name'	=> $name
		);
		$route = $this->createRoute($spec);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\RouteInterface',$route);
		$this->assertEquals($spec['key'], $route->getRouteKey());

		/* route startup */
		$this->assertFalse($route->isIgnoreConfigStartupTasks());
		$this->assertFalse($route->isPrependStartupTasks());
		$this->assertFalse($route->isStartupDisabled());
		$this->assertFalse($route->isStartupTasks());
		$this->assertEquals(array(), $route->getStartupTasks());
		$this->assertFalse($route->isExcludedStartupTasks());
		$this->assertEquals(array(), $route->getExcludedStartupTasks());
	
		/* route intercept */
		$this->assertTrue($route->isPreFilteringEnabled());
		$this->assertFalse($route->isPreFilters());
		$this->assertEquals(array(), $route->getPreFilters());
		$this->assertFalse($route->isExcludedPreFilters());
		$this->assertEquals(array(), $route->getExcludedPreFilters());
		$this->assertTrue($route->isPostFilteringEnabled());
		$this->assertFalse($route->isPostFilters());
		$this->assertEquals(array(), $route->getPostFilters());
		$this->assertFalse($route->isExcludedPostFilters());
		$this->assertEquals(array(), $route->getExcludedPostFilters());
			
		/* route access */
		$this->assertFalse($route->isPublicAccess());
		$this->assertFalse($route->isInternalOnlyAccess());
		$this->assertFalse($route->isAclAccessIgnored());
		$this->assertFalse($route->isAccessAllowed('anycode'));

		/* route view */
		$this->assertFalse($route->isViewDisabled());
		$this->assertFalse($route->isManualView());
		$this->assertFalse($route->isViewPackage());
		$this->assertEquals(null, $route->getViewPackage());
		$this->assertEquals('html', $route->getFormat());

		/* route action */
		$this->assertEquals($name, $route->getActionName());
		$this->assertEquals($name, $route->findActionName());

		/* route validation */
		$this->assertTrue($route->isInputValidation());
		$this->assertTrue($route->isThrowOnValidationFailure());
		$this->assertEquals(500, $route->getValidationErrorCode());
		$this->assertEquals(array(), $route->getValidationSpecList());
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
			'key'		  => 'my-route',
		);
		$route = $this->createRoute($spec);

		$this->assertTrue($route->isPrependStartupTasks());
		$this->assertTrue($route->isIgnoreConfigStartupTasks());
		$this->assertTrue($route->isStartupDisabled());
		$this->assertTrue($route->isStartupTasks());
		$this->assertEquals(
			$spec['startup']['tasks'], 
			$route->getStartupTasks()
		);
		$this->assertTrue($route->isExcludedStartupTasks());
		$this->assertEquals(
			$spec['startup']['excluded-tasks'], 
			$route->getExcludedStartupTasks()
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

			'key'		  => 'my-route',
			'action-name' => 'MyAction',
		);
		$route = $this->createRoute($spec);

		/* route intercept */
		$this->assertFalse($route->isPreFilteringEnabled());
		$this->assertTrue($route->isPreFilters());
		$this->assertEquals(
			$spec['intercept']['include-pre'], 
			$route->getPreFilters()
		);

		$this->assertTrue($route->isExcludedPreFilters());
		$this->assertEquals(
			$spec['intercept']['exclude-pre'], 
			$route->getExcludedPreFilters()
		);
		$this->assertFalse($route->isPostFilteringEnabled());
		$this->assertTrue($route->isPostFilters());
		$this->assertEquals(
			$spec['intercept']['include-post'], 
			$route->getPostFilters()
		);
		$this->assertTrue($route->isExcludedPostFilters());
		$this->assertEquals(
			$spec['intercept']['exclude-post'], 
			$route->getExcludedPostFilters()
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
				'is-ignore-acl'	=> true,
				'acl-access'    => array(
					'get'   => array('admin', 'editor', 'guest'),
					'put'	=> array('admin', 'editor'),
					'post'	=> array('admin', 'editor'),
					'delete'=> array('admin')
				),
			),
			'key'		  => 'my-route',
			'action-name' => 'MyAction',
		);
		$route = $this->createRoute($spec);

		/* route access */
		$this->assertTrue($route->isPublicAccess());
		$this->assertTrue($route->isInternalOnlyAccess());
		$this->assertTrue($route->isAclAccessIgnored());
		
		$result = $route->isAccessAllowed(
			$spec['access']['acl-access']['get'], 
			'get'
		);
		$this->assertTrue($result);

		$result = $route->isAccessAllowed(
			$spec['access']['acl-access']['put'], 
			'put'
		);
		$this->assertTrue($result);

		$result = $route->isAccessAllowed(
			$spec['access']['acl-access']['post'], 
			'post'
		);
		$this->assertTrue($result);


		$result = $route->isAccessAllowed('admin', 'delete');
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
			'key'		  => 'my-route',
			'action-name' => 'MyAction',
		);
		$route = $this->createRoute($spec);

		/* route access */
		$this->assertFalse($route->isPublicAccess());
		$this->assertFalse($route->isInternalOnlyAccess());
		$this->assertFalse($route->isAclAccessIgnored());
		
		$result = $route->isAccessAllowed($spec['access']['acl-access']);
		$this->assertTrue($result);
		$this->assertTrue($route->isAccessAllowed('admin'));
		$this->assertTrue($route->isAccessAllowed('publisher'));
		$this->assertTrue($route->isAccessAllowed('editor'));
		$this->assertFalse($route->isAccessAllowed('guest'));
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
			'key'		  => 'my-route',
		);
		$route = $this->createRoute($spec);

		/* route access */
		$this->assertFalse($route->isPublicAccess());
		$this->assertFalse($route->isInternalOnlyAccess());
		$this->assertFalse($route->isAclAccessIgnored());
		
		$result = $route->isAccessAllowed($spec['acl-access']);
		$this->assertTrue($result);
		$this->assertTrue($route->isAccessAllowed('admin'));
		$this->assertTrue($route->isAccessAllowed('publisher'));
		$this->assertTrue($route->isAccessAllowed('editor'));
		$this->assertFalse($route->isAccessAllowed('guest'));
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
			'action-name' => 'MyAction',
			'key'		  => 'my-route',
		);
		$route = $this->createRoute($spec);

		$this->assertTrue($route->isViewDisabled());
		$this->assertTrue($route->isViewPackage());
		$this->assertEquals('appfuel:page.my-page', $route->getViewPackage());
		$this->assertEquals('html', $route->getFormat());
	}

	/**
	 * is-view must be true in order to set is-manual-view
	 *
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
			'action-name' => 'MyAction',
			'key'		  => 'my-route',
		);
		$route = $this->createRoute($spec);

		$this->assertTrue($route->isManualView());
		$this->assertTrue($route->isViewPackage());
		$this->assertEquals('appfuel:page.my-page', $route->getViewPackage());
		$this->assertEquals('html', $route->getFormat());
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeAction()
	{
		$spec = array(
			'action' => array(
				'map' => array(
					'get'  => 'GetAction',
					'put'  => 'PutAction',
					'post' => 'PostAction',
					'delete' => 'DeleteAction'
				),
			),
			'key' => 'my-route',
		);
		$route = $this->createRoute($spec);
		$this->assertEquals('GetAction', $route->findActionName('get'));
		$this->assertEquals('PutAction', $route->findActionName('put'));
		$this->assertEquals('PostAction', $route->findActionName('post'));
		$this->assertEquals('DeleteAction', $route->findActionName('delete'));
		$this->assertFalse($route->findActionName('clii'));
		
	}

	/**
	 * @test
	 * @return	MvcRouteDetail
	 */
	public function routeInputAction()
	{
		$spec = array(
			'action-name' => 'blah',
			'key'		  => 'my-route',
			'validation' => array(
				'ignore'			=> true,
				'throw-on-failure'  => false,
				'error-code'		=> 404,
				'validate' => array(
					array(
						'fields'     => array('field-a', 'field-b'),
						'location'   => 'post',
						'filters'  => array(
							'int' => array(
								'options' => array('max' => 100),
								'error'   => 'some error message',
							),
						),
					),
				),		
			)
		);
		$route = $this->createRoute($spec);
		$this->assertFalse($route->isInputValidation());
		$this->assertFalse($route->isThrowOnValidationFailure());
		$this->assertEquals(404, $route->getValidationErrorCode());
		$this->assertEquals(
			$spec['validation']['validate'], 
			$route->getValidationSpecList()
		);
	}
}
