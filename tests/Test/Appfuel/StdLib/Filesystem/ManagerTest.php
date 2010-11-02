<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Tests
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Test\Appfuel\StdLib\Filesystem;

/* import */
use Appfuel\StdLib\Filesystem\Manager 	as FilesystemManager;

/**
 * @package 	Appfuel
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * File Path
	 * @var string
	 */
	protected $path = NULL;

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->path = 'example'      . DIRECTORY_SEPARATOR . 
					  'filesystem'   . DIRECTORY_SEPARATOR .
					  'text_file.txt';

	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->path);
	}

	/**
	 * Test classNameToFileName
	 * The will decode a class (full name space) to its relative file location.
	 * It also handles the legacy pre PHP 5.3 naming convention using the 
	 * underscore to seperate namespaces
	 *
	 * Assert:	regular namespaces are decoded correctly
	 * Assert:	legacy namespace are decoded correctly
	 */
	public function testClassNameToFileName()
	{  
		$className = 'My\Class\Instance';
		$result    = FilesystemManager::classNameToFileName($className);
		$expected  = 'My'    . DIRECTORY_SEPARATOR .
					 'Class' . DIRECTORY_SEPARATOR .
					'Instance.php';

		$this->assertEquals($expected, $result);

		$className = 'My_Class_Instance';
		$result    = FilesystemManager::classNameToFileName($className);
		
		$this->assertEquals($expected, $result);

	}

	/**
	 * Test getAbsolutePath
	 * This will return the absolute file path if it exists by itself or
	 * it exists when joined with a path from the include path
	 *
	 * Assert:	An absolute file path that actually points to the file
	 * 			will return that path
	 * Assert: 	A relative path that exists in the include path will 
	 * 			return the full absolute path for that file because 
	 * 			exists	
	 * Assert:	A path for a file that does not exist returns FALSE		
	 */
	public function testGetAbsoluteInPath()
	{
		$includePath = get_include_path();

		/* file that is known to exist */
		$abPath = AF_TEST_PATH   . DIRECTORY_SEPARATOR .	
				  'example'      . DIRECTORY_SEPARATOR . 
				  'filesystem'   . DIRECTORY_SEPARATOR .
				  'text_file.txt';


		/* file will not exist in path */
		set_include_path('some/path');

		/* 
		 * proves that paths that point to files that exist
		 * will be returns as themselves
		 */
		$path = FilesystemManager::getAbsolutePath($abPath);
		$this->assertEquals($abPath, $path);
		
		set_include_path(AF_TEST_PATH); 
		$relPath = 'example'      . DIRECTORY_SEPARATOR . 
				   'filesystem'   . DIRECTORY_SEPARATOR .
				   'text_file.txt';

		$path = FilesystemManager::getAbsolutePath($relPath);
		$this->assertEquals($abPath, $path);
		set_include_path($includePath); 

		$path = FilesystemManager::getAbsolutePath('/does/not/exist');
		$this->assertFalse($path);
	}

	/**
	 * Test requireFile
	 * This uses the FileInterface
	 * php get_included_files is used to prove or disprove the existence
	 * of included files
	 *
	 * Assert: 	a file that does not exists returns FALSE
	 * Assert:	a file that does exists returns TRUE AND
	 * 			the file is included into mememory.
	 */
	public function testRequireFile()
	{
		$file = $this->getMock(
			'\Appfuel\StdLib\Filesystem\File',
			array('isFile'),
			array('/path/to/knowwhere')
		);
		
		$file->expects($this->once())
			->method('isFile')
			->will($this->returnValue(FALSE));

		$this->assertFalse(FilesystemManager::requireFile($file));
		$files = get_included_files();
		$this->assertNotContains('/path/to/knowehere', $files);

		$file = $this->getMock(
			'\Appfuel\StdLib\Filesystem\File',
			array('isFile', 'getRealPath'),
			array('/path/to/knowwhere')
		);
		
		$file->expects($this->once())
			->method('isFile')
			->will($this->returnValue(TRUE));

		/* file that is known to exist */
		$abPath = AF_TEST_PATH   . DIRECTORY_SEPARATOR .	
				  'example'      . DIRECTORY_SEPARATOR . 
				  'filesystem'   . DIRECTORY_SEPARATOR .
				  'text_file.txt';

		$file->expects($this->once())
			 ->method('getRealPath')
			 ->will($this->returnValue($abPath));

		/* prove this file has not already been loaded */
		$files = get_included_files();
		$this->assertNotContains($abPath, $files);

		/* this is a text file so we don't want the text
		 * sent as output
		 */
		ob_start();	
		$this->assertTrue(FilesystemManager::requireFile($file));
		ob_end_clean();
		
		$files = get_included_files();
		$this->assertContains($abPath, $files);
	}
}

