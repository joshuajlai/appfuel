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

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Operation\OperationModel,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * Test only success cases for the operation model
 */
class OperationModelTest extends BaseTestCase
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
		$this->model = new OperationModel();
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
		$data1 = array(
			'id'				=> 99,
			'name'				=> 'some-operation',
			'description'		=> 'this is an operation',
			'opClass'			=> 'business',
		);

		$data2 = $data1;
		$data3 = $data1;
		
		$data2['opClass']		= 'infra';
		$data3['opClass']		= 'ui';

		return array(
			array($data1),
			array($data2),
			array($data3),
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
			'Appfuel\Framework\Domain\Operation\OperationDomainInterface',
			$this->model
		);


	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->model, $this->model->_marshal($data));
		$this->assertEquals($data['id'], $this->model->getId());
		$this->assertEquals($data['name'], $this->model->getName());
		$this->assertEquals(
			$data['description'], 
			$this->model->getDescription()
		);
		$this->assertEquals($data['opClass'], $this->model->getOpClass());

		$state = $this->model->_getDomainState();
		$this->assertTrue($state->isMarshal());
	}
}
