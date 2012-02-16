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
namespace TestFuel\Unit\View\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Resource\PackageFileList;


/**
 * The package file list holds a list of files categories by file type
 */
class PackageFileListTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var PackageFileList
	 */
	protected $list = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->list = new PackageFileList();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\PackageFileListInterface',
			$this->list
		);
	}
}
