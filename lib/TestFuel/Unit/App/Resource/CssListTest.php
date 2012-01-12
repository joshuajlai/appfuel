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
namespace TestFuel\Unit\App\Resource;

use Appfuel\App\Resource\CssList,
	TestFuel\TestCase\BaseTestCase;

/**
 * This is a file list that only allows css files to be added
 */
class CssListTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var CssList
	 */
	protected $list = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->list = new CssList();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\App\Resource\FileList',
			$this->list
		);
		
		$this->assertEquals('css', $this->list->getType());
		$this->assertEquals(0, $this->list->count());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testNonCssFile()
	{
		$this->list->addFile('myjs.js');
	}

	/**
	 * @return	null
	 */
	public function testAddCssFiles()
	{
		$files = array(
			'file1.css',
			'file2.css',
			'file3.css'
		);
		$this->assertSame($this->list, $this->list->loadFiles($files));
		$this->assertEquals($files,	$this->list->getFiles());
	}
}
