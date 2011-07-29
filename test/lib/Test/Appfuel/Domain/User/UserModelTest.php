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
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DomainModelInterface',
			$this->user
		);
	}
}
