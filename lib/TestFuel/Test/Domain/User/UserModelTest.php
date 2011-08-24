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
namespace TestFuel\Test\Domain\User;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\User\UserModel;

/**
 * Test the user members and automated get/setter
 */
class UserModelTest extends BaseTestCase
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
		$this->model = new UserModel();
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
		$data = array(
			'id'			=> 99,
			'loginName'		=> 'rscottb',
			'firstName'		=> 'Robert',
			'lastName'		=> 'Scott-Buccleuch',
			'email'			=> 'rsb.code@gmail.com',
			'activityCode'	=> 'active',
			'dateCreated'	=> 'jan-06-2011',
			'lastAccessed'  => 'june-08-2011'
		);

		return array(array($data));
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
	}

	/**
	 * @dataProvider	provideValidModelData
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->model, $this->model->_marshal($data));
		$this->assertEquals($data['id'], $this->model->getId());
		$this->assertEquals($data['loginName'], $this->model->getLoginName());
		$this->assertEquals($data['firstName'], $this->model->getFirstName());
		$this->assertEquals($data['lastName'], $this->model->getLastName());
		$this->assertEquals($data['email'], $this->model->getEmail());
		$this->assertEquals(
			$data['activityCode'], 
			$this->model->getActivityCode()
		);
		$this->assertEquals(
			$data['dateCreated'], 
			$this->model->getDateCreated());
		$this->assertEquals(
			$data['lastAccessed'], 
			$this->model->getLastAccessed()
		);

		$state = $this->model->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}
}
