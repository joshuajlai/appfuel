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
}
