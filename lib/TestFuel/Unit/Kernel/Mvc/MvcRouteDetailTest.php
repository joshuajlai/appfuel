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
	public function testEmptyDetail()
	{
		$detail = new MvcRouteDetail(array());
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetailInterface',
			$detail
		);

		$this->assertFalse($detail->isPublicAccess());
		$this->assertFalse($detail->isInternalOnlyAccess());
		$this->assertFalse($detail->isAccessAllowed('some-code'));

		$this->assertFalse($detail->isSkipPreFilters());
		$this->assertFalse($detail->isPreFilters());
		$this->assertEquals(array(), $detail->getPreFilters());
		$this->assertFalse($detail->isExcludedPreFilters());
		$this->assertEquals(array(), $detail->getExcludedPreFilters());

		$this->assertFalse($detail->isSkipPostFilters());
		$this->assertFalse($detail->isPostFilters());
		$this->assertEquals(array(), $detail->getPostFilters());
		$this->assertFalse($detail->isExcludedPostFilters());
		$this->assertEquals(array(), $detail->getExcludedPostFilters());

		$this->assertFalse($detail->isViewDetail());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testPublicAccess()
	{
		$data = array('is-public' => true);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isPublicAccess());

		$data = array('is-public' => false);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isPublicAccess());

		/*
		 * Anything thats not strict bool true is false
		 */
		$data = array('is-public' => 'true');
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isPublicAccess());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testInternalAccess()
	{
		$data = array('is-internal' => true);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isInternalOnlyAccess());

		$data = array('is-internal' => false);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isInternalOnlyAccess());

		/*
		 * Anything thats not strict bool true is false
		 */
		$data = array('is-internal' => 'true');
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isInternalOnlyAccess());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testAclAccessOneStringNotPublic()
	{
		$data = array('acl-access' => 'my-admin');
		$detail = new MvcRouteDetail($data);

		$this->assertFalse($detail->isPublicAccess());	
		$this->assertTrue($detail->isAccessAllowed('my-admin'));
		$this->assertFalse($detail->isAccessAllowed('my-staff'));
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testAclAccessOneStringPublicRoute()
	{
		$data = array(
			'is-public'  => true,
			'acl-access' => 'my-admin'
		);
		$detail = new MvcRouteDetail($data);

		$this->assertTrue($detail->isPublicAccess());	
		$this->assertTrue($detail->isAccessAllowed('my-admin'));
		
		/* true because we are a public route */
		$this->assertTrue($detail->isAccessAllowed('my-staff'));
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testAclAccessEmptyString()
	{
		$data = array('acl-access' => '');
		$detail = new MvcRouteDetail($data);

		$this->assertFalse($detail->isAccessAllowed('my-admin'));
		
		/* empty string is not a valid acl code */
		$this->assertFalse($detail->isAccessAllowed(''));
		
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testAclAccessEmpty()
	{
		$data = array('acl-access' => array());
		$detail = new MvcRouteDetail($data);

		$this->assertFalse($detail->isAccessAllowed('my-admin'));
		
		/* empty string is not a valid acl code */
		$this->assertFalse($detail->isAccessAllowed(array()));
		$this->assertFalse($detail->isAccessAllowed(array()));
	}

	/**
	 * @depends	testEmptyDetail
	 * @return null
	 */
	public function testAclAccessManyCodes()
	{
		$data = array(
			'acl-access' => array('admin', 'publisher', 'editor')
		);
		$detail = new MvcRouteDetail($data);
		
		$this->assertTrue($detail->isAccessAllowed('admin'));
		$this->assertTrue($detail->isAccessAllowed('publisher'));
		$this->assertTrue($detail->isAccessAllowed('editor'));
		$this->assertFalse($detail->isAccessAllowed('guest'));

		$this->assertTrue($detail->isAccessAllowed($data['acl-access']));

		$list = array('guest', 'me', 'you');
		$this->assertFalse($detail->isAccessAllowed($list));


		$list = array('guest', 'guest', 'me', 'me', 'you');
		$this->assertFalse($detail->isAccessAllowed($list));
		
		$list = array('guest', 'me', 'you', 'admin');
		$this->assertTrue($detail->isAccessAllowed($list));
	
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testSkipPreFilters()
	{
		$data = array(
			'intercept' => array('is-skip-pre' => true)
		);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isSkipPreFilters());

		$data = array(
			'intercept' => array('is-skip-pre' => false)
		);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isSkipPreFilters());

		/* has to be strict bool true */
		$data = array(
			'intercept' => array('is-skip-pre' => 'true')
		);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isSkipPreFilters());
	}

	/**
	 * @return	null
	 */
	public function testPreFiltersSingleString()
	{
		$data = array(
			'intercept' => array(
				'include-pre' => 'my-filter-class-name'
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isPreFilters());
		$this->assertFalse($detail->isExcludedPreFilters());
	
		$expected = array('my-filter-class-name');
		$this->assertEquals($expected, $detail->getPreFilters());
	}

	/**
	 * @return	null
	 */
	public function testPreFiltersManyFilters()
	{
		$data = array(
			'intercept' => array(
				'include-pre' => array('my-filter','your-filter')
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isPreFilters());
		$this->assertFalse($detail->isExcludedPreFilters());
		
		$expected = $data['intercept']['include-pre'];
		$this->assertEquals($expected, $detail->getPreFilters());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @return				null
	 */
	public function testPreFilterInvalidObject_Failure()
	{
		$data = array(
			'intercept' => array(
				'include-pre' => new StdClass()
			)
		);	
		$detail = new MvcRouteDetail($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @return				null
	 */
	public function testPreFilterInvalidBool_Failure()
	{
		$data = array(
			'intercept' => array(
				'include-pre' => true
			)
		);	
		$detail = new MvcRouteDetail($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @return				null
	 */
	public function testPreFilterInvalidInt_Failure()
	{
		$data = array(
			'intercept' => array(
				'include-pre' => 12345
			)
		);	
		$detail = new MvcRouteDetail($data);
	}

    /**
     * @return  null
     */
    public function testPreFiltersExcludeSingleString()
    {
        $data = array(
            'intercept' => array(
                'exclude-pre' => 'my-filter-class-name'
            )
        );
        $detail = new MvcRouteDetail($data);
        $this->assertFalse($detail->isPreFilters());
        $this->assertTrue($detail->isExcludedPreFilters());

        $expected = array('my-filter-class-name');
        $this->assertEquals($expected, $detail->getExcludedPreFilters());
    }

	/**
	 * @return	null
	 */
	public function testPreFiltersExcludedManyFilters()
	{
		$data = array(
			'intercept' => array(
				'exclude-pre' => array('my-filter','your-filter')
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isPreFilters());
		$this->assertTrue($detail->isExcludedPreFilters());
		
		$expected = $data['intercept']['exclude-pre'];
		$this->assertEquals($expected, $detail->getExcludedPreFilters());
	}

	/**
	 * @return	null
	 */
	public function testPostFiltersSingleString()
	{
		$data = array(
			'intercept' => array(
				'include-post' => 'my-filter-class-name'
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isPostFilters());
		$this->assertFalse($detail->isExcludedPostFilters());
	
		$expected = array('my-filter-class-name');
		$this->assertEquals($expected, $detail->getPostFilters());
	}

	/**
	 * @return	null
	 */
	public function testPostManyFilters()
	{
		$data = array(
			'intercept' => array(
				'include-post' => array('my-filter','your-filter')
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isPostFilters());
		$this->assertFalse($detail->isExcludedPostFilters());
		
		$expected = $data['intercept']['include-post'];
		$this->assertEquals($expected, $detail->getPostFilters());
	}

	/**
	 * @return	null
	 */
	public function testExcludedPostFiltersSingleString()
	{
		$data = array(
			'intercept' => array(
				'exclude-post' => 'my-filter-class-name'
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isPostFilters());
		$this->assertTrue($detail->isExcludedPostFilters());
	
		$expected = array('my-filter-class-name');
		$this->assertEquals($expected, $detail->getExcludedPostFilters());
	}

	/**
	 * @return	null
	 */
	public function testExludedPostManyFilters()
	{
		$data = array(
			'intercept' => array(
				'exclude-post' => array('my-filter','your-filter')
			)
		);	
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isPostFilters());
		$this->assertTrue($detail->isExcludedPostFilters());
		
		$expected = $data['intercept']['exclude-post'];
		$this->assertEquals($expected, $detail->getExcludedPostFilters());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testSkipPostFilters()
	{
		$data = array(
			'intercept' => array('is-skip-post' => true)
		);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isSkipPostFilters());

		$data = array(
			'intercept' => array('is-skip-post' => false)
		);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isSkipPostFilters());

		/* has to be strict bool true */
		$data = array(
			'intercept' => array('is-skip-post' => 'true')
		);
		$detail = new MvcRouteDetail($data);
		$this->assertFalse($detail->isSkipPostFilters());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testViewEmptyDetail()
	{
		$data = array('view-detail' => array());
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isViewDetail());
	
		$view = $detail->getViewDetail();
		$this->assertTrue($view->isView());
		$this->assertEquals('general', $view->getStrategy());
		$this->assertEquals(array(), $view->getParams());
		$this->assertNull($view->getMethod());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testViewDetailFullData()
	{
		$data = array(
			'view-detail' => array(
				'is-view'  => false,
				'strategy' => 'my-strategy',
				'params'   => array('a', 'b'),
				'method'   => 'my_method'
			)
		);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isViewDetail());
	
		$view = $detail->getViewDetail();
		$this->assertFalse($view->isView());
		$this->assertEquals('my-strategy', $view->getStrategy());
		$this->assertEquals(array('a', 'b'), $view->getParams());
		$this->assertEquals('my_method', $view->getMethod());
	}

	/**
	 * @depends	testEmptyDetail
	 * @return	null
	 */
	public function testViewDetailCorrectInterface()
	{
		$view = $this->getMock('Appfuel\Kernel\Mvc\MvcViewDetailInterface');
		$data = array('view-detail' => $view);
		$detail = new MvcRouteDetail($data);
		$this->assertTrue($detail->isViewDetail());
		$this->assertSame($view, $detail->getViewDetail());	
	}
}
