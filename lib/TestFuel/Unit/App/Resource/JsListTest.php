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

use StdClass,
	SplFileInfo,
	Appfuel\App\Resource\JsList,
	TestFuel\TestCase\BaseTestCase;

/**
 * This is a file list that only allows javascript files to be added
 */
class JsTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var JsList
	 */
	protected $list = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->list = new JsList();
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
		
		$this->assertEquals('js', $this->list->getType());
		$this->assertEquals(0, $this->list->count());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testNonJsFile()
	{
		$this->list->addFile('mycss.css');
	}

	/**
	 * @return	null
	 */
	public function testAddJsFiles()
	{
		$files = array(
			'file1.js',
			'file2.js',
			'file3.js'
		);
		$this->assertSame($this->list, $this->list->loadFiles($files));
		$this->assertEquals($files,	$this->list->getFiles());
	}
}
