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
	Appfuel\App\Resource\FileList,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class FileListTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var FileList
	 */
	protected $list = null;

	/**
	 * First param in constructor represents the file type
	 * @var string
	 */
	protected $type = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->type = 'asset';
		$this->whiteList = array('png', 'swf', 'jpg', 'gif');
		$this->list = new FileList($this->type, $this->whiteList);
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
			'Appfuel\App\Resource\FileListInterface',
			$this->list
		);
		$this->assertEquals($this->type, $this->list->getType());
		$this->assertEquals(0, $this->list->count());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddFileGetFiles()
	{
		$file1 = 'some/path/to/a/image.png';
		$file2 = 'some/path/to/a/movie.swf';
		$file3 = 'some/path/to/a/thumbnail.jpg';
		$file4 = 'some/path/to/a/logo.gif';
		$this->assertSame($this->list, $this->list->addFile($file1));
		
		$expected = array($file1);
		$this->assertEquals($expected, $this->list->getFiles());
		
		$expected[] = $file2;
		$this->assertSame($this->list, $this->list->addFile($file2));
		$this->assertEquals($expected, $this->list->getFiles());

		$expected[] = $file3;
		$this->assertSame($this->list, $this->list->addFile($file3));
		$this->assertEquals($expected, $this->list->getFiles());

		$expected[] = $file4;
		$this->assertSame($this->list, $this->list->addFile($file4));
		$this->assertEquals($expected, $this->list->getFiles());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideEmptyStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddFileEmptyString_Failure($file)
	{
		$this->list->addFile($file);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddFileInvalidString_Failure($file)
	{
		$this->list->addFile($file);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testAddFileNotValidatedBadExt_Failure()
	{
		$file = 'some/file/with/bad/file.unknown';
		$this->list->addFile($file);
	}

	/**
	 * @return	null
	 */
	public function testAddFileValidationDisabled()
	{
		$list = new FileList('general');
		$file1 = 'myfile';
		$file2 = 'myotherfile.js';
		$file3 = 'newfile.css';
		$this->assertEquals(array(), $list->getFiles());
	
		$this->assertSame($list, $list->addFile($file1));
		$this->assertSame($list, $list->addFile($file2));
		$this->assertSame($list, $list->addFile($file3));
			
		$this->assertEquals(3, $list->count());

		$expected = array($file1, $file2, $file3);
		$this->assertEquals($expected, $list->getFiles());
	}

	/**
	 * @return	null
	 */
	public function testIteratorFiles()
	{
		$files = array(
			'mypng.png',
			'myswf.swf',
			'myjpg.jpg',
			'mygif.gif'
		);

		$this->assertSame($this->list, $this->list->loadFiles($files));
		$this->assertEquals(4, $this->list->count());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(0, $this->list->key());
		$this->assertEquals($files[0], $this->list->current());
		
		/* advance to myswf */
		$this->assertNull($this->list->next());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(1, $this->list->key());
		$this->assertEquals($files[1], $this->list->current());

		/* advance to myjpg */
		$this->assertNull($this->list->next());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(2, $this->list->key());
		$this->assertEquals($files[2], $this->list->current());

		/* advance to mygif */
		$this->assertNull($this->list->next());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(3, $this->list->key());
		$this->assertEquals($files[3], $this->list->current());

		/* advance out of range */
		$this->assertNull($this->list->next());
		$this->assertFalse($this->list->valid());
		$this->assertNull($this->list->key());
		$this->assertFalse($this->list->current());

		/* back to mypng */
		$this->assertNull($this->list->rewind());
		$this->assertTrue($this->list->valid());
		$this->assertEquals(0, $this->list->key());
		$this->assertEquals($files[0], $this->list->current());
	}
}
