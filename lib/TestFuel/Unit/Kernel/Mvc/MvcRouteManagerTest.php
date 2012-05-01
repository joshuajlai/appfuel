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
	Appfuel\Kernel\Mvc\MvcRouteManager;

/**
 */
class MvcRouteManagerTest extends BaseTestCase
{
	/**
	 * @return	null
	 */
	public function setUp()
	{
		MvcRouteManager::clearRouteMap();
		MvcRouteManager::clearCache();
	}

	/**
	 * @return	null
	 */	
	public function testGetRouteMapAddRoute()
	{
		$this->assertEquals(array(), MvcRouteManager::getRouteMap());

		$keyA = 'routeA';
		$keyB = 'routeB';
		$keyC = 'routeC';
		$nsA  = 'my\a\namespace';
		$nsB  = 'my\b\namespace';
		$nsC  = 'my\c\namespace';
		$this->assertNull(MvcRouteManager::addRoute($keyA, $nsA));
	
		$expected = array($keyA => $nsA);
		$this->assertEquals($expected, MvcRouteManager::getRouteMap());
		$this->assertEquals($nsA, MvcRouteManager::getNamespace($keyA));

		$this->assertNull(MvcRouteManager::addRoute($keyB, $nsB));
		
		$expected[$keyB] = $nsB;
		$this->assertEquals($expected, MvcRouteManager::getRouteMap());
		$this->assertEquals($nsA, MvcRouteManager::getNamespace($keyA));
		$this->assertEquals($nsB, MvcRouteManager::getNamespace($keyB));

		$this->assertNull(MvcRouteManager::addRoute($keyC, $nsC));
			
		$expected[$keyC] = $nsC;
		$this->assertEquals($expected, MvcRouteManager::getRouteMap());
		$this->assertEquals($nsA, MvcRouteManager::getNamespace($keyA));
		$this->assertEquals($nsB, MvcRouteManager::getNamespace($keyB));
		$this->assertEquals($nsC, MvcRouteManager::getNamespace($keyC));
	
		$this->assertNull(MvcRouteManager::clearRouteMap());
		$this->assertEquals(array(), MvcRouteManager::getRouteMap());
	}

	/**
	 * Empty string is a valid key, it usually represents the default route
	 * @return	null
	 */
	public function testAddRouteWithEmptyStringKey()
	{
		$this->assertNull(MvcRouteManager::addRoute('', 'my\namespace'));
		$this->assertEquals('my\namespace', MvcRouteManager::getNamespace(''));
	}

	/**
	 * Empty namespace are allowed but discouraged
	 * @return	null
	 */
	public function testAddRouteWithEmptyStringNamespace()
	{
		$this->assertNull(MvcRouteManager::addRoute('', ''));
		$this->assertEquals('', MvcRouteManager::getNamespace(''));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithIntKey_Failure()
	{
		MvcRouteManager::addRoute(222, 'my\namespace');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithIntNamespace_Failure()
	{
		MvcRouteManager::addRoute('key', 12345);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithObjectKey_Failure()
	{
		MvcRouteManager::addRoute(new StdClass(), 'my\namespace');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithObjectNamespace_Failure()
	{
		MvcRouteManager::addRoute('key', new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithArrayKey_Failure()
	{
		MvcRouteManager::addRoute(array(1,2,3), 'my\namespace');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddRouteWithArrayNamespace_Failure()
	{
		MvcRouteManager::addRoute('key', array(1,2,3));
	}

	/**
	 * @return	null
	 */
	public function testSetRouteMapNoExistingRoutes()
	{
		$list = array(
			'routeA' => 'my\a\namespace',
			'routeB' => 'my\b\namespace',
			'routeC' => 'my\c\namespace',
		);
		
		$this->assertEquals(array(), MvcRouteManager::getRouteMap());
		$this->assertNull(MvcRouteManager::setRouteMap($list));
		$this->assertEquals($list, MvcRouteManager::getRouteMap());
	}

	/**
	 * @return	null
	 */
	public function testSetRouteMapWithExistingRoutes()
	{
		$list1 = array(
			'routeA' => 'my\a\namespace',
			'routeB' => 'my\b\namespace',
			'routeC' => 'my\c\namespace',
		);
		
		$this->assertEquals(array(), MvcRouteManager::getRouteMap());
		$this->assertNull(MvcRouteManager::setRouteMap($list1));
		$this->assertEquals($list1, MvcRouteManager::getRouteMap());

		$list2 = array(
			'routeD' => 'my\d\namespace',
			'routeE' => 'my\e\namespace',
			'routeF' => 'my\f\namespace',
		);

		/* this will replace the whole map */
		$this->assertNull(MvcRouteManager::setRouteMap($list2));
		$this->assertEquals($list2, MvcRouteManager::getRouteMap());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetRouteIndexArray_Failure()
	{
		$list = array('routeA', 'routeB', 'routeC');
		MvcRouteManager::setRouteMap($list);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetRouteBadNamespace_Failure()
	{
		$list = array('routeA'=> 'ns\a', 'routeB'=>12345, 'routeC'=>'ns\c');
		MvcRouteManager::setRouteMap($list);
	}

	/**
	 * @return	null
	 */
	public function testLoadMapWithNoRoutes()
	{
        $list = array(
            'routeA' => 'my\a\namespace',
            'routeB' => 'my\b\namespace',
            'routeC' => 'my\c\namespace',
        );

        $this->assertEquals(array(), MvcRouteManager::getRouteMap());
        $this->assertNull(MvcRouteManager::loadRouteMap($list));
        $this->assertEquals($list, MvcRouteManager::getRouteMap());
	}

    /**
     * @return  null
     */
    public function testLoadRouteMapWithExistingRoutes()
    {
        $list1 = array(
            'routeA' => 'my\a\namespace',
            'routeB' => 'my\b\namespace',
            'routeC' => 'my\c\namespace',
        );

        $this->assertEquals(array(), MvcRouteManager::getRouteMap());
        $this->assertNull(MvcRouteManager::setRouteMap($list1));
        $this->assertEquals($list1, MvcRouteManager::getRouteMap());

        $list2 = array(
            'routeD' => 'my\d\namespace',
            'routeE' => 'my\e\namespace',
            'routeF' => 'my\f\namespace',
        );

		$expected = array_merge($list1, $list2);
        /* this will replace the whole map */
        $this->assertNull(MvcRouteManager::loadRouteMap($list2));
        $this->assertEquals($expected, MvcRouteManager::getRouteMap());
    }

	/**
	 * @return	null
	 */
	public function testAddToCacheGetCacheClearCacheHandlers()
	{
		$this->assertEquals(array(), MvcRouteManager::getAllCache());
		
		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler1 = $this->getMock($hInterface);
		$key1 = 'master-a';

		$this->assertNull(MvcRouteManager::addToCache($key1, $handler1));

		$expected = array($key1 => $handler1);
		$this->assertEquals($expected, MvcRouteManager::getAllCache());

		$handler2 = $this->getMock($hInterface);
		$key2 = 'master-b';
		$this->assertNull(MvcRouteManager::addToCache($key2, $handler2));
		
		$expected[$key2] = $handler2;
		$this->assertEquals($expected, MvcRouteManager::getAllCache());
			
		$this->assertNull(MvcRouteManager::clearCache());
		$this->assertEquals(array(), MvcRouteManager::getAllCache());
	}

	/**
	 * @return	null
	 */
	public function testAddToCacheWithPointers()
	{
		$this->assertEquals(array(), MvcRouteManager::getAllCache());
		
		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler1 = $this->getMock($hInterface);
		$key1 = 'master-a';
		$this->assertNull(MvcRouteManager::addToCache($key1, $handler1));

		$key2 = 'alias-a';
		$key3 = 'alias-b';
		$key4 = 'alias-c';
		$this->assertNull(MvcRouteManager::addToCache($key2, $key1));
		$this->assertNull(MvcRouteManager::addToCache($key3, $key1));
		$this->assertNull(MvcRouteManager::addToCache($key4, $key1));
		
		$this->assertSame($handler1, MvcRouteManager::getFromCache($key1));	
		$this->assertSame($handler1, MvcRouteManager::getFromCache($key2));	
		$this->assertSame($handler1, MvcRouteManager::getFromCache($key3));	
		$this->assertSame($handler1, MvcRouteManager::getFromCache($key4));	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheKeyObject_Failure()
	{
		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler = $this->getMock($hInterface);
		MvcRouteManager::addToCache(new StdClass(), $handler);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheKeyInt_Failure()
	{
		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler = $this->getMock($hInterface);
		MvcRouteManager::addToCache(12345, $handler);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheKeyArray_Failure()
	{
		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler = $this->getMock($hInterface);
		MvcRouteManager::addToCache(array(1,2,3), $handler);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheHandlerIsArray_Failure()
	{
		MvcRouteManager::addToCache('key', array(1,2,3));
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheHandlerIsNotInterface_Failure()
	{
		MvcRouteManager::addToCache('key', new StdClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddToCacheHandlerIsInt_Failure()
	{
		MvcRouteManager::addToCache('key', 123);
	}

	/**
	 * @expectedException	LogicException
	 * @return				null
	 */
	public function testAddToCacheAliasPointToHandlerNotSet()
	{
		MvcRouteManager::addToCache('alias-b', 'master-a');
	}

	/**
	 * @return	null
	 */ 
	public function testGetRouteDetailWhenInCache()
	{
		$value = 'i am a fake route detail';

		$hInterface = 'Appfuel\Kernel\Mvc\MvcRouteHandlerInterface';
		$handler = $this->getMock($hInterface);
		$handler->expects($this->once())
				->method('getRouteDetail')
				->will($this->returnValue($value));		
		$this->assertEquals(array(), MvcRouteManager::getAllCache());

		MvcRouteManager::addToCache('my-key', $handler);
		$this->assertSame($value, MvcRouteManager::getRouteDetail('my-key'));
	}

	public function testCreateRouteHandler()
	{
		$key = 'my-route';
		$ns  = 'TestFuel\Functional\Action\TestRouteManager';
		MvcRouteManager::addRoute($key, $ns);
		
		$handler = MvcRouteManager::createRouteHandler($key);
		$class = "$ns\\RouteHandler";
		$this->assertInstanceOf($class, $handler);
	}

	/**
	 * @return	null
	 */	
	public function testGetRouteThatExistsButNotLoader()
	{
		$key = 'my-route';
		$key2 = 'alias-a';
		$key3 = 'alias-b';
		$key4 = 'alias-c';

		$ns  = 'TestFuel\Functional\Action\TestRouteManager';
		MvcRouteManager::addRoute($key, $ns);
		MvcRouteManager::addRoute($key2, $ns);
		MvcRouteManager::addRoute($key3, $ns);
		MvcRouteManager::addRoute($key4, $ns);

		$this->assertEquals(array(), MvcRouteManager::getAllCache());
		
		$detail = MvcRouteManager::getRouteDetail($key);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcRouteDetail',
			$detail
		);
		$this->assertSame($detail, MvcRouteManager::getRouteDetail($key2));		
		$this->assertSame($detail, MvcRouteManager::getRouteDetail($key3));		
		$this->assertSame($detail, MvcRouteManager::getRouteDetail($key4));		
	}

}
