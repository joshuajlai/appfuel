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
	Appfuel\Domain\Operation\OperationalRoute,
	Appfuel\Framework\Action\ControllerNamespace;

/**
 * Test only success cases for the operation model
 */
class OperationalRouteTest extends BaseTestCase
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
		$data1 = array(
			'id'				=> 99,
			'routeString'		=> 'error/handler/invalid',
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
}
