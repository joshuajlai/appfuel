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
use Appfuel\StdLib\Filesystem\File		as AfFile;

/**
 * @package 	Appfuel
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Directory where all the sample files are kept
     * @var string
     */
    protected $fileDir = NULL;

    /**
     * Path to Ini file used to test ini parsing
     * @var string
     */
    protected $iniFile = NULL;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->fileDir = AF_TEST_PATH   . DIRECTORY_SEPARATOR .
                         'example'      . DIRECTORY_SEPARATOR .
                         'filesystem';

        $this->iniFile = $this->fileDir . DIRECTORY_SEPARATOR .
                         'sample_a.ini';

        $this->file = new afFile($this->iniFile);
    }

	/**
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->file);
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
	 * Test parseIni
	 * Testing the default parameters of section False meaning combine all data into one array AND
	 * mode is normal the php constant INI_SCANNER_NORMAL. Also making basic assertions that the 
	 * data is coming back correctly. Techinally not necessary cause php handles this, more of a
	 * for piece of mind
	 */ 
	public function testParseIniNoSectionNoContants()
	{
		$data = FilesystemManager::parseIni($this->file);
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('one', $data);
		$this->assertArrayHasKey('five', $data);
		$this->assertArrayHasKey('animal', $data);
		$this->assertArrayHasKey('url', $data);
		$this->assertArrayHasKey('path', $data);
		$this->assertArrayHasKey('phpversion', $data);

		$this->assertEquals(1, $data['one']);
		$this->assertEquals(5, $data['five']);
	
		/* this is a constant in the ini file but
		 * never defined so its just a string
		 */
		$this->assertEquals('BIRD', $data['animal']);

		$this->assertEquals("/usr/local/bin", $data['path']);
		$this->assertEquals("http://www.example.com/~username", $data['url']);

		$data = $data['phpversion'];
		$this->assertInternalType('array', $data);
		$this->assertEquals(4, count($data));
		$this->assertEquals(5.0, $data[0]);
		$this->assertEquals(5.1, $data[1]);
		$this->assertEquals(5.2, $data[2]);
		$this->assertEquals(5.3, $data[3]);

	}

	/**
	 * Test parseIni
	 * Assert Constants can be used in ini file
	 */
	public function testParseIniNoSectionsWithConstant()
	{
		define('BIRD', 'hawk');
		$data = FilesystemManager::parseIni($this->file);

		$this->assertEquals('hawk', $data['animal']);
	}

	/**
	 * Test parseIni 
	 * When section is TRUE parseIni will return all sections in 
	 * an associative array. We also test that the data is parsed to
	 * how we expect it.
	 */
	public function testParseIniWithSections()
	{
		$animal = 'BIRD';
		if (defined('BIRD')) {
			$animal = BIRD;
		}
		$data = FilesystemManager::parseIni($this->file, TRUE);
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('first_section', $data);
		$this->assertArrayHasKey('second_section', $data);
		$this->assertArrayHasKey('third_section', $data);

		$sec = $data['first_section'];
		$this->assertInternalType('array', $data);
		
		$this->assertArrayHasKey('one', $sec);
		$this->assertArrayHasKey('five', $sec);
		$this->assertArrayHasKey('animal', $sec);

        $this->assertEquals(1, $sec['one']);
        $this->assertEquals(5, $sec['five']);
        $this->assertEquals($animal, $sec['animal']);

		$sec = $data['second_section'];
		$this->assertInternalType('array', $sec);
		
		$this->assertArrayHasKey('path', $sec);
		$this->assertArrayHasKey('url', $sec);
		$this->assertEquals("/usr/local/bin", $sec['path']);
		$this->assertEquals("http://www.example.com/~username", $sec['url']);


		$sec = $data['third_section'];
		$this->assertArrayHasKey('phpversion', $sec);

		$sec = $sec['phpversion'];
		$this->assertInternalType('array', $sec);
		$this->assertEquals(4, count($sec));
		$this->assertEquals(5.0, $sec[0]);
		$this->assertEquals(5.1, $sec[1]);
		$this->assertEquals(5.2, $sec[2]);
		$this->assertEquals(5.3, $sec[3]);
	}
}

