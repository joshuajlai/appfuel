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

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Operation\OperationModel,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * Test all the failure cases for the OperationalModel
 */
class OperationModel_FailureTest extends BaseTestCase
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

	public function provideNotNonEmptyString()
	{
		return	array(
			array(''),
			array(12345),
			array(1.23454),
			array(array()),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->model);
	}

	/**
	 * @dataProvider		provideNotNonEmptyString
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testFailedSetName($param)
	{
		$this->model->setName($param);
	}

	/**
	 * @dataProvider		provideNotNonEmptyString
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testFailedSetOpClass($param)
	{
		$this->model->setOpClass($param);
	}

	/**
	 * Valid opClasses are business, infra or ui. Anything else will throw
	 * an exception
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testSetOpClassNotValidType()
	{
		$this->model->setOpClass('not-business-infra-or-ui');
	}
}
