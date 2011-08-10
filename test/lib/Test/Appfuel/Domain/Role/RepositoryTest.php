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
namespace Test\Appfuel\Domain\Role;

use Test\AfTestCase as ParentTestCase,
	Appfuel\Domain\Role\Repository;

/**
 * Test all repository interactions. Note these tests run against the database
 */
class RepositoryTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Repository
	 */
	protected $repo = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->repo = new Repository();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->repo);
	}

	/**
	 * @return null
	 */
	public function testHasInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Repository\RepositoryInterface',
			$this->repo
		);
	}
}
