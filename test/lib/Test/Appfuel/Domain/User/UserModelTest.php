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
namespace Test\Appfuel\Db\Adapter;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Domain\User\UserModel;

/**
 * Test the adapters ability to wrap mysqli
 */
class UserModelTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Error
	 */
	protected $user = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->user = new UserModel();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->user);
	}

	/**
	 * @return	array
	 */
	public function provideValidUser()
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
			$this->user
		);
	}

	/**
	 * @dataProvider	provideValidUser
	 * @return	null
	 */
	public function testMarshal(array $data)
	{
		$this->assertSame($this->user, $this->user->_marshal($data));
		$this->assertEquals($data['id'], $this->user->getId());
		$this->assertEquals($data['loginName'], $this->user->getLoginName());
		$this->assertEquals($data['firstName'], $this->user->getFirstName());
		$this->assertEquals($data['lastName'], $this->user->getLastName());
		$this->assertEquals($data['email'], $this->user->getEmail());
		$this->assertEquals(
			$data['activityCode'], 
			$this->user->getActivityCode()
		);
		$this->assertEquals($data['dateCreated'], $this->user->getDateCreated());
		$this->assertEquals(
			$data['lastAccessed'], 
			$this->user->getLastAccessed()
		);

		$state = $this->user->_getDomainState();
		$this->assertEquals('marshal', $state->getState());
		$this->assertTrue($state->isMarshal());
	}
}
