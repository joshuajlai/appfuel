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
	Appfuel\Domain\Operation\Repository,
	Appfuel\Framework\Action\ActionControllerDetail;

/**
 * The operation repository handles loading the static operations file,
 * finding operations from the array in that file, adding removing, and 
 * selecting operations out of the database
 */
class RepositoryTest extends BaseTestCase
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
		$this->repo = null;
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

	/**
	 * This repo has one inherited immutable member the ormFactory and
	 * one defined in its own constructor which it the file path of the 
	 * generated operation list.
	 *
	 * @return null
	 */
	public function testImmutableMembers()
	{
		$file = $this->repo->getOperationListFile();
		$this->assertInstanceOf(
			'Appfuel\Framework\File\FrameworkFile',
			$file
		);

		$expected = AF_BASE_PATH . '/codegen/operations.php';
		$this->assertEquals($expected, $file->getFullPath());
	}
}
