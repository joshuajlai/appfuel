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
namespace Test\Appfuel\Filesystem;

/*
 * Autoloading has not been established so we need to manaully 
 * include this file
 */

require_once 'Appfuel/Filesystem/File.php';

/* import */
use Appfuel\Filesystem\File as afFile;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * File
	 * System under test
	 * @var asFile
	 */
	protected $file = NULL;

	/**
	 * File Path
	 * Used in the constructor: location of file on disk
	 * @var string
	 */
	protected $path = NULL;

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->path = AF_TEST_PATH   . DIRECTORY_SEPARATOR . 
					  'example'      . DIRECTORY_SEPARATOR . 
					  'filesystem'   . DIRECTORY_SEPARATOR .
					  'text_file.txt';

		$this->file = new afFile($this->path);
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->file);
		unset($this->path);
	}

	/**
	 * Test __construct and getPath
	 * The constructor assigns path. Path is immutable so only the
	 * public getter is available
	 *
	 * Assert: 	getPath return then path used in the constructor 
	 */
	public function testConstructorGetPath()
	{
		$this->assertEquals($this->path, $this->file->getRealPath());
	}

	/**
	 * Test exists
	 * Wrapper for php method file_exists and is based on whatever is being
	 * pointed to by the path member
	 *
	 * Assert:	for the file in setup exists returns TRUE
	 * Assert: 	php's file_exists returns TRUE when given getPath
	 * Assert: 	given a file known not to exist then exist returns FALSE
	 */
	public function testExists()
	{
		$this->assertTrue($this->file->exists());
		$this->assertTrue(file_exists($this->file->getRealPath()));

		/* prove path does not exist */
		$path = '/path/to/nowhere.txt';
		$this->assertFalse(file_exists($path));

		$file = new afFile($path);
		$this->assertFalse($file->exists());
	}

}

