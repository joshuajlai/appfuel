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
namespace TestFuel\Test\Domain\Operation;

use TestFuel\TestCase\FrameworkTestCase,
	Appfuel\Domain\Operation\OperationModel,
	Appfuel\Domain\Operation\OperationalRoute,
	Appfuel\Framework\Action\ControllerNamespace;

/**
 * Test only success cases for the operation model
 */
class OperationalRouteTest extends FrameworkTestCase
{
	/**
	 * System under test
	 * @var Error
	 */
	protected $model = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->model = new OperationalRoute();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->model);
	}

	/**
	 * @return	array
	 */
	public function provideValidModelData()
	{
		$op = $this->getMock(
			'Appfuel\Framework\Domain\Operation\OperationInterface'
		);
		$data1 = array(
			'id'					=> 99,
			'operation'				=> $op,
			'controllerNamespace'	=> 'Namespace\For\Controller\Ns',
			'accessPolicy'			=> 'public',
			'routeString'			=> 'error/handler/invalid',
			'defaultFormat'			=> 'html',
			'requestType'			=> 'http',
			'filters'				=> array(
				'pre'  => array('filter1', 'filter2', 'filter3'),
				'post' => array('filter4', 'filter5', 'filter6')
			),	
		);

		return array(
			array($data1),
		);
	}

	/**
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->model
		);
		$this->assertInstanceOf(
			'Appfuel\Framework\Domain\Operation\OperationalRouteInterface',
			$this->model
		);
	}

	/**
	 * @dataProvider provideValidModelData
	 * @return		 null
	 */
	public function testMarshal(array $data)
	{
        $this->assertSame($this->model, $this->model->_marshal($data));
        $this->assertEquals($data['id'], $this->model->getId());
        $this->assertEquals($data['operation'], $this->model->getOperation());
        $this->assertEquals(
			$data['routeString'],
			$this->model->getRouteString()
		);
		$this->assertEquals($data['filters'], $this->model->getFilters());
		$this->assertEquals(
			$data['defaultFormat'],
			$this->model->getDefaultFormat()
		);

        $state = $this->model->_getDomainState();
        $this->assertTrue($state->isMarshal());
	}

	/**
	 * @return	null
	 */
	public function testGetSetId()
	{
		$this->assertNull($this->model->getId(), 'default value is null');
		$this->assertSame($this->model, $this->model->setId(99));
		$this->assertEquals(99, $this->model->getId());
	}

	/**
	 * @return	null
	 */
	public function testGetSetOperation()
	{
		$this->assertNull(
			$this->model->getOperation(), 
			'default value is null'
		);
		$op = $this->getMock(
			'Appfuel\Framework\Domain\Operation\OperationInterface'
		);

		$this->assertSame($this->model, $this->model->setOperation($op));
		$this->assertSame($op, $this->model->getOperation());
	}

	/**
	 * The controller namespace is set with the full namespace of the action
	 * controller. The setter takes the string and uses it to create a 
	 * Appfuel\Framework\Action\ControllerNamespace object.
	 *
	 * @return	nul
	 */
	public function testGetSetControllerNamespace()
	{
		$this->assertNull(
			$this->model->getControllerNamespace(),
			'this is the default value'
		);

		$this->assertSame(
			$this->model, 
			$this->model->setControllerNamespace('Name\Space\To\Controller')
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Action\ControllerNamespaceInterface',
			$this->model->getControllerNamespace()
		);
	}

	/**
	 * Access Policy has only two valid values (public|private)
	 * 
	 * @return	null
	 */
	public function testAccessPolicy()
	{
		$this->assertNull($this->model->getAccessPolicy());
		$this->assertSame(
			$this->model, 
			$this->model->setAccessPolicy('public')
		);
		$this->assertEquals('public', $this->model->getAccessPolicy());

		$this->assertSame(
			$this->model, 
			$this->model->setAccessPolicy('private')
		);
		$this->assertEquals('private', $this->model->getAccessPolicy());
	}
	/**
	 * Request type has only three valid values (http|http-ajax|cli)
	 * 
	 * @return	null
	 */
	public function testRequestType()
	{
		$this->assertNull($this->model->getRequestType());
		$this->assertSame(
			$this->model, 
			$this->model->setRequestType('http')
		);
		$this->assertEquals('http', $this->model->getRequestType());

		$this->assertSame(
			$this->model, 
			$this->model->setRequestType('http-ajax')
		);
		$this->assertEquals('http-ajax', $this->model->getRequestType());

		$this->assertSame(
			$this->model, 
			$this->model->setRequestType('cli')
		);
		$this->assertEquals('cli', $this->model->getRequestType());
	}

	/**
	 * @return	null
	 */
	public function testRouteString()
	{
		$this->assertNull($this->model->getRouteString());

		$route = 'any/string/really';
		$this->assertSame($this->model, $this->model->setRouteString($route));
		$this->assertEquals($route, $this->model->getRouteString());
	}

	/**
	 * @depends	testMarshal
	 * @return	null
	 */
	public function testAddGetPreFilters()
	{
		$filters = array(
			'pre'  => array(),
			'post' => array()
		);
		$this->assertEquals($filters, $this->model->getFilters());

		$this->assertNull($this->model->_getDomainState());

		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter1', 'pre'),
			'exposes a fluent interface'
		);

		$state = $this->model->_getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Orm\Domain\DomainState',
			$state
		);

		$this->assertTrue($state->isDirty());
		
		$expected = array('filter1');
		$this->assertEquals($expected, $this->model->getPreFilters());
	
		/* 
		 * will return everything in pre that getPreFilters returned 
		 * plus an empty post
		 */	
		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter2', 'pre'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2');
		$this->assertEquals($expected, $this->model->getPreFilters());

		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter3', 'pre'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2', 'filter3');
		$this->assertEquals($expected, $this->model->getPreFilters());

		$expected = array(
			'pre'  => $expected,
			'post' => array()
		);
		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * @depends	testAddGetPreFilters
	 * @return	null
	 */
	public function testAddGetPostFilters()
	{
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter1', 'post'),
			'exposes a fluent interface'
		);

		$state = $this->model->_getDomainState();
		$this->assertInstanceOf(
			'Appfuel\Orm\Domain\DomainState',
			$state
		);

		$this->assertTrue($state->isDirty());
		
		$expected = array('filter1');
		$this->assertEquals($expected, $this->model->getPostFilters());
	
		/* 
		 * will return everything in pre that getPreFilters returned 
		 * plus an empty post
		 */	
		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter2', 'post'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2');
		$this->assertEquals($expected, $this->model->getPostFilters());

		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	
		$this->assertSame(
			$this->model, 
			$this->model->addFilter('filter3', 'post'),
			'exposes a fluent interface'
		);
		
		$expected = array('filter1', 'filter2', 'filter3');
		$this->assertEquals($expected, $this->model->getPostFilters());

		$expected = array(
			'pre'  => array(),
			'post' => $expected
		);
		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * @depends	testAddGetPostFilters
	 * @return	null
	 */
	public function testAddPreAndPostFilters()
	{
		$this->model->addFilter('filter1', 'PRE')
					->addFilter('filter2', 'POST')
					->addFilter('filter3', 'pRe')
					->addFilter('filter4', 'POst')
					->addFilter('filter5', 'pre')
					->addFilter('filter6', 'post');

		$expectedPre = array('filter1', 'filter3', 'filter5');
		$this->assertEquals($expectedPre, $this->model->getPreFilters());

		$expectedPost = array('filter2', 'filter4', 'filter6');
		$this->assertEquals($expectedPost, $this->model->getPostFilters());

		$expected = array(
			'pre'	=> $expectedPre,
			'post'	=> $expectedPost
		);

		$this->assertEquals($expected, $this->model->getFilters());

	}

	/**
	 * @depends	testAddPreAndPostFilters
	 * @return	null
	 */
	public function testAddPrePostFilterWithDuplications()
	{
		$this->model->addFilter('filter1', 'pre')
					->addFilter('filter1', 'pre')
					->addFilter('filter2', 'post')
					->addFilter('filter2', 'post')
					->addFilter('filter3', 'pre')
					->addFilter('filter4', 'post')
					->addFilter('filter4', 'post')
					->addFilter('filter4', 'post')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter5', 'pre')
					->addFilter('filter6', 'post');

		$expectedPre = array('filter1', 'filter3', 'filter5');
		$this->assertEquals($expectedPre, $this->model->getPreFilters());

		$expectedPost = array('filter2', 'filter4', 'filter6');
		$this->assertEquals($expectedPost, $this->model->getPostFilters());

		$expected = array(
			'pre'	=> $expectedPre,
			'post'	=> $expectedPost
		);

		$this->assertEquals($expected, $this->model->getFilters());
	}

	/**
	 * Each time you run setFilters it wipes out any old filters already added
	 * 
	 * @depends	testAddPreAndPostFilters
	 * @return	null
	 */
	public function testSetFiltersAlternateWays()
	{
		$filters = array(
			'pre'  => 'filter1',
			'post' => 'filter2'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);

		$expected = array(
			'pre'	=> array('filter1'),
			'post'	=> array('filter2')
		);

		$this->assertEquals($expected, $this->model->getFilters());

		$filters = array(
			'pre'  => 'filter1'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);
		$expected = array(
			'pre'	=> array('filter1'),
			'post'	=> array()
		);

		$this->assertEquals($expected, $this->model->getFilters());

		$filters = array(
			'post' => 'filter1'
		);
		$this->assertSame(
			$this->model,
			$this->model->setFilters($filters),
			'exposes fluent interface'
		);

		$expected = array(
			'pre'	=> array(),
			'post'	=> array('filter1')
		);

		$this->assertEquals($expected, $this->model->getFilters());

		/* an empty array is ignored */
		$this->assertSame(
			$this->model,
			$this->model->setFilters(array()),
			'exposes fluent interface'
		);

		$this->assertEquals($expected, $this->model->getFilters());
	}
}
