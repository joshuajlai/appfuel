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
namespace TestFuel\Test\Domain\Role;

use TestFuel\TestCase\BaseTestCase,
	Appfuel\Domain\Role\RoleModel;

/**
 * Test the member properties and marshalling
 */
class RoleModelTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var Role
	 */
	protected $model = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->model = new RoleModel();
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
	public function provideValidUser()
	{
		$data = array(
			'id'			=> 99,
			'name'			=> 'super-user',
			'authLevel'		=> 'root',
			'description'	=> 'Has full access to the entire system',
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
	 * @dataProvider	provideValidUser
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->model, $this->model->_marshal($data));
		$this->assertEquals($data['id'], $this->model->getId());
		$this->assertEquals($data['name'], $this->model->getName());
		$this->assertEquals($data['authLevel'], $this->model->getAuthLevel());
		$this->assertEquals(
			$data['description'], 
			$this->model->getDescription()
		);
		
		$state = $this->model->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}
}
