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
namespace TestFuel\Test\Domain\Route;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Route\RouteDomain,
	Appfuel\Domain\Action\ActionDomain,
	Appfuel\Domain\InterceptFilter\InterceptFilterDomain,
	Appfuel\Domain\InterceptFilter\InterceptFilterCollection;

/**
 * Test the action domain describes the action controller
 */
class RouteDomainTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var RouteDomain
	 */
	protected $route = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->route = new RouteDomain();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->route = null;
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$action = $this->getMock(
			'Appfuel\Framework\Domain\Action\ActionDomainInterface'
		);

		$filterA = new InterceptFilterDomain(1);
		$filterA->setKey('my-filter-a')
				->setType('pre');
		$filterB = new InterceptFilterDomain(2);
		$filterB->setKey('my-filter-b')
				->setType('post');

		$filters = new InterceptFilterCollection();
		$filters->add($filterA)
			    ->add($filterB);

		$data = array(
			'id'		=> 99,
			'routeKey'	=> 'my-route',
			'action'	=> $action,
			'filters'	=> $filters	
		);

		return array(array($data));
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->route
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Domain\Route\RouteDomainInterface',
			$this->route
		);
	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->route, $this->route->_marshal($data));
		$this->assertEquals($data['id'], $this->route->getId());
		$this->assertEquals($data['routeKey'], $this->route->getRouteKey());

		$state = $this->route->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}

	/**
	 * The route key must be a valid non empty string
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRouteKey()
	{
		$key = 'route-x';
		$this->assertNull($this->route->getRouteKey());
		$this->assertSame(
			$this->route,
			$this->route->setRouteKey($key),
			'uses fluent interface'
		);
		$this->assertEquals($key, $this->route->getRouteKey());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAction()
	{
		$action = $this->getMock(
			'Appfuel\Framework\Domain\Action\ActionDomainInterface'
		);
		$this->assertNull($this->route->getAction());
		$this->assertSame(
			$this->route,
			$this->route->setAction($action),
			'uses fluent interface'
		);
		$this->assertEquals($action, $this->route->getAction());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsPublic()
	{
		$this->assertFalse($this->route->isPublic());
		$this->assertFalse($this->route->getIsPublic());

		$this->assertSame(
			$this->route,
			$this->route->setIsPublic(true),
			'uses fluent interface'
		);
	
		$this->assertTrue($this->route->isPublic());
		$this->assertTrue($this->route->getIsPublic());

		$this->assertSame(
			$this->route,
			$this->route->setIsPublic(false),
			'uses fluent interface'
		);
		$this->assertFalse($this->route->isPublic());
		$this->assertFalse($this->route->getIsPublic());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testFilters()
	{
		$collection = new InterceptFilterCollection();
		$this->assertNull($this->route->getFilters());
	
		$this->assertSame(
			$this->route,
			$this->route->setFilters($collection),
			'uses fluent interface'
		);	
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_EmptyStringFailure()
	{
		$this->route->setRouteKey('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_IntFailure()
	{
		$this->route->setRouteKey(12345);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_ArrayFailure()
	{
		$this->route->setRouteKey(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetRouteKey_ObjectFailure()
	{
		$this->route->setRouteKey(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_EmptyStringFailure()
	{
		$this->route->setIsPublic('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_StringTrueFailure()
	{
		$this->route->setIsPublic('true');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_StringFalseFailure()
	{
		$this->route->setIsPublic('false');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_ArrayFailure()
	{
		$this->route->setIsPublic(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_Int1Failure()
	{
		$this->route->setIsPublic(1);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_Int0Failure()
	{
		$this->route->setIsPublic(0);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends	testInterface
	 * @return	null
	 */
	public function testSetIsPublic_ObjectFailure()
	{
		$this->route->setIsPublic(new StdClass());
	}
}
