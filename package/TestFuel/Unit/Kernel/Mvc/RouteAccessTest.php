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
	Appfuel\Kernel\Mvc\RouteAccess;

/**
 */
class RouteAccessTest extends BaseTestCase
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
	 * @test
	 * @return RouteAccess
	 */
	public function createRouteAccess()
	{
		$access = new RouteAccess();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RouteAccessInterface',
			$access
		);

		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function publicAccess(RouteAccess $access)
	{
		$this->assertFalse($access->isPublicAccess());
		
		$this->assertSame($access, $access->enablePublicAccess());
		$this->assertTrue($access->isPublicAccess());
		
		$this->assertSame($access, $access->disablePublicAccess());
		$this->assertFalse($access->isPublicAccess());

		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function internalAccess(RouteAccess $access)
	{
		$this->assertFalse($access->isInternalOnlyAccess());
		
		$this->assertSame($access, $access->enableInternalOnlyAccess());
		$this->assertTrue($access->isInternalOnlyAccess());
		
		$this->assertSame($access, $access->disableInternalOnlyAccess());
		$this->assertFalse($access->isInternalOnlyAccess());

		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function ignoreAclAccess(RouteAccess $access)
	{
		$this->assertFalse($access->isAclAccessIgnored());
		
		$this->assertSame($access, $access->ignoreAclAccess());
		$this->assertTrue($access->isAclAccessIgnored());
		
		$this->assertSame($access, $access->useAclAccess());
		$this->assertFalse($access->isAclAccessIgnored());

		return $access;
	}

	/**
	 * Acl codes can be defined for input methods that appfuel supports. 
	 * When true acl codes will be applied individually for get, put, post,
	 * delete and cli
	 *
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function aclForEachMethod(RouteAccess $access)
	{
		$this->assertFalse($access->isAclForEachMethod());
		
		$this->assertSame($access, $access->useAclForEachMethod());
		$this->assertTrue($access->isAclForEachMethod());
		
		$this->assertSame($access, $access->useAclForAllMethods());
		$this->assertFalse($access->isAclForEachMethod());

		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapNoMethods(RouteAccess $access)
	{
		$map = array('admin', 'publisher', 'editor');
		$this->assertEmpty($access->getAclMap());
		$this->assertSame($access, $access->setAclMap($map));
		$this->assertEquals($map, $access->getAclMap());

		$this->assertSame($access, $access->setAclMap(array()));
		$this->assertEmpty($access->getAclMap());
	
		return $access;
	}

	/**
	 * Since this acl map is for all methods it should be an indexed 
	 * array of strings not an associative array
	 *
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapCodeNotStringFailure(RouteAccess $access)
	{
		$msg = 'all acl codes must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$map = array('admin', 12345, 'publisher', 'editor');
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethods(RouteAccess $access)
	{
		$map = array(
			'get'	 => array('admin', 'publisher', 'editor'),
			'put'	 => array('admin', 'publisher'),
			'post'	 => array('admin', 'publisher'),
			'delete' => array('admin')
		);
		
		$access->useAclForEachMethod();
		$this->assertEmpty($access->getAclMap());
		$this->assertSame($access, $access->setAclMap($map));
		$this->assertEquals($map, $access->getAclMap());
	
		/* restore default setting */
		$access->useAclForAllMethods();
		$this->assertSame($access, $access->setAclMap(array()));
		$this->assertEmpty($access->getAclMap());
	
		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethodNotStringFailure(RouteAccess $access)
	{
		$map = array(
			'get' => array('admin', 'publisher'),
			1234  => array('publisher')
		);
		$msg = 'the method acl codes are mapped to must be a non empty string';
		$access->useAclForEachMethod();
		$this->setExpectedException('DomainException', $msg);		
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethodEmptyStringFailure(RouteAccess $access)
	{
		$map = array(
			'get' => array('admin', 'publisher'),
			''    => array('publisher')
		);
		$msg = 'the method acl codes are mapped to must be a non empty string';
		$access->useAclForEachMethod();
		$this->setExpectedException('DomainException', $msg);		
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethodNotArrayFailure(RouteAccess $access)
	{
		$map = array(
			'get'	=> array('admin', 'publisher'),
			'put'   => array('publisher'),
			'post'	=> 'admin'
		);
		$msg = 'list of codes for -(post) must be an array';
		$access->useAclForEachMethod();
		$this->setExpectedException('DomainException', $msg);		
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethodItemNotStringFailure(RouteAccess $access)
	{
		$map = array(
			'get'	=> array('admin', 'publisher'),
			'put'   => array('publisher'),
			'post'	=> array('admin', 12345)
		);
		$msg = 'acl code for -(post) must be a non empty string';
		$access->useAclForEachMethod();
		$this->setExpectedException('DomainException', $msg);		
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function setAclMapMethodItemEmptyStringFailure(RouteAccess $access)
	{
		$map = array(
			'get'	=> array('admin', 'publisher'),
			'put'   => array('publisher'),
			'post'	=> array('admin', '')
		);
		$msg = 'acl code for -(post) must be a non empty string';
		$access->useAclForEachMethod();
		$this->setExpectedException('DomainException', $msg);		
		$access->setAclMap($map);
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function isAccessAllowedPublic(RouteAccess $access)
	{
		$access->enablePublicAccess();

		/* will all ways return true */
		$this->assertTrue($access->isAccessAllowed('admin'));
		$this->assertTrue($access->isAccessAllowed(''));
		$this->assertTrue($access->isAccessAllowed('admin', 'put'));

		$this->assertTrue($access->isAccessAllowed(12345));

		$access->disablePublicAccess();
		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function isAccessAllowedIgnoreAclAccess(RouteAccess $access)
	{
		$access->disablePublicAccess();
		$access->ignoreAclAccess();

		/* will all ways return true */
		$this->assertTrue($access->isAccessAllowed('admin'));
		$this->assertTrue($access->isAccessAllowed(''));
		$this->assertTrue($access->isAccessAllowed('admin', 'put'));

		$this->assertTrue($access->isAccessAllowed(12345));

		$access->disablePublicAccess();
		$access->useAclAccess();
		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function isAccessAllowedNotMapped(RouteAccess $access)
	{
		$access->disablePublicAccess()
			   ->useAclAccess()
			   ->useAclForAllMethods();

		$map = array('admin', 'publisher', 'editor');
		$access->setAclMap($map);

		$this->assertTrue($access->isAccessAllowed($map));
		$this->assertTrue($access->isAccessAllowed(array('admin','publisher')));
		$this->assertTrue($access->isAccessAllowed(array('admin')));
		$this->assertTrue($access->isAccessAllowed(array('publisher')));
		$this->assertTrue($access->isAccessAllowed(array('editor')));

		$this->assertTrue($access->isAccessAllowed('admin'));
		$this->assertTrue($access->isAccessAllowed('publisher'));
		$this->assertTrue($access->isAccessAllowed('editor'));

		$this->assertFalse($access->isAccessAllowed('guest'));
		$this->assertFalse($access->isAccessAllowed('ADMIN'));
		$this->assertFalse($access->isAccessAllowed(array('a', 'b', 'c')));

		$this->assertFalse($access->isAccessAllowed(12345));
		$this->assertFalse($access->isAccessAllowed(null));
		$this->assertFalse($access->isAccessAllowed(array(1,23,3)));

		/* method is ignored because map applies to all methods */
		$this->assertTrue($access->isAccessAllowed($map), 'put');

		$access->setAclMap(array());
		return $access;
	}

	/**
	 * @test
	 * @depends	createRouteAccess
	 * @return	RouteAccess
	 */
	public function isAccessAllowedMapped(RouteAccess $access)
	{
		$access->disablePublicAccess()
			   ->useAclAccess()
			   ->useAclForEachMethod();

		$map = array(
			'get'    => array('admin', 'publisher', 'editor', 'guest'),
			'put'    => array('admin', 'publisher'),
			'post'   => array('admin', 'publisher', 'editor'),
			'delete' => array('admin'),
		);
		$access->setAclMap($map);

		/* when no method is given false is returned */
		$this->assertFalse($access->isAccessAllowed('admin'));
	
		$this->assertTrue($access->isAccessAllowed($map['get'], 'get'));	
		$this->assertTrue($access->isAccessAllowed($map['put'], 'put'));	
		$this->assertTrue($access->isAccessAllowed($map['post'], 'post'));	
		$this->assertTrue($access->isAccessAllowed($map['delete'], 'delete'));

		$this->assertTrue($access->isAccessAllowed('admin', 'get'));
		$this->assertTrue($access->isAccessAllowed('publisher', 'get'));
		$this->assertTrue($access->isAccessAllowed('editor', 'get'));
		$this->assertTrue($access->isAccessAllowed('guest', 'get'));
		$this->assertFalse($access->isAccessAllowed('other', 'get'));

		$codes = array('guest', 'admin', 'other');
		$this->assertTrue($access->isAccessAllowed($codes, 'get'));
		
		$badCodes = array('other', 'foo', 'bar');
		$this->assertFalse($access->isAccessAllowed($badCodes, 'get'));
			
		$this->assertTrue($access->isAccessAllowed('admin', 'put'));
		$this->assertTrue($access->isAccessAllowed('publisher', 'put'));
		$this->assertTrue($access->isAccessAllowed($codes, 'put'));
		$this->assertFalse($access->isAccessAllowed($badCodes, 'put'));

		$this->assertTrue($access->isAccessAllowed('admin', 'post'));
		$this->assertTrue($access->isAccessAllowed('publisher', 'post'));
		$this->assertTrue($access->isAccessAllowed('editor', 'post'));
		$this->assertTrue($access->isAccessAllowed($codes, 'post'));
		$this->assertFalse($access->isAccessAllowed($badCodes, 'post'));

		$this->assertTrue($access->isAccessAllowed('admin', 'delete'));
		$this->assertTrue($access->isAccessAllowed($codes, 'delete'));
		$this->assertFalse($access->isAccessAllowed($badCodes, 'delete'));
	}

}
