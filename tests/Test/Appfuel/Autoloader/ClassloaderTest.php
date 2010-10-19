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
namespace Test\Appfuel\Autoloader;

/*
 * Autoloading has not been established so we need to manaully 
 * include this file
 */
require_once 'Appfuel/Filesystem/Manager.php';

require_once 'Appfuel/Autoloader/LoaderInterface.php';
require_once 'Appfuel/Autoloader/Classloader.php';

/* import */
use Appfuel\Autoloader\Classloader as Autoloader;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Auto Loader
	 * System under test
	 * @var Autoloader
	 */
	protected $loader = NULL;

	/**
	 * @return void
	 */
	public function setUp()
	{
		$this->backupIncludePath();
		$path = AF_TEST_PATH . DIRECTORY_SEPARATOR . 
				'example'    . DIRECTORY_SEPARATOR . 
				'autoload'   . DIRECTORY_SEPARATOR .
				'classes';

		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		$this->loader = new Autoloader();
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		$this->restoreIncludePath();
		unset($this->loader);
	}

	/**
	 * Backup Inclulde Path
	 * Utility used in testing.
	 *
	 * @return void
	 */
	public function backupIncludePath()
	{
		$this->includePath = get_include_path();
	}

	/**
	 * Get Old Include Path
	 * Utility used in testing.
	 *
	 * @return string
	 */
	public function getOldIncludePath()
	{
		return $this->includePath;
	}

	/**
	 * Restore Inclulde Path
	 * Utility used in testing.
	 *
	 * @return void
	 */
	public function restoreIncludePath()
	{
		set_include_path($this->getOldIncludePath());
	}

	/**
	 * Test __construct
	 * The constructor assigns the namespace separator for php 5.3
	 *
	 * Assert: 	getRegisteredMethodName returns 'loadClass'
	 */
	public function testConstructor()
	{
		$this->assertEquals('loadClass', $this->loader->getRegisteredMethod());
	}

	/**
	 * Test decodeNamespaceToPath
	 * This basically turns the namespace separator into directory
	 * separators.
	 *
	 * Note: DIRECTORY_SEPARATOR will be different depending on your system.
	 * 		 appfuel is primarly made to run on posix compliant systems
	 *
	 * Assert: 	given a name My\Class\Instance and assuming the constant
	 * 			DIRECTORY_SEPARATOR is '/' then decodeNamespaceToPath returns
	 * 			string 'My/Class/Instance'
	 */ 
	public function ttestDecodeNamespaceToPath()
	{
		$className = 'My\Class\Instance';
		$result    = $this->loader->decodeNamespaceToPath($className);
		$expected  = 'My'    . DIRECTORY_SEPARATOR . 
					 'Class' . DIRECTORY_SEPARATOR .
					 'Instance';

		$this->assertEquals($expected, $result);
	}

	/**
	 * Test loadClass
	 *
	 * Assert: 	the class we are loading is not loaded AND
	 * 			isLoaded returns FALSE
	 * Assert: 	loadClass returns NULL AND
	 * 			the class name now exists AND 
	 * 			isLoaded returns TRUE
	 */
	public function testLoadClass()
	{
		$className = 'My\TestClassA\Instance';
		$this->assertFalse(class_exists($className));
		$this->assertFalse($this->loader->isLoaded($className));

		$result    = $this->loader->loadClass($className);
		$this->assertNull($result);
		$this->assertTrue(class_exists($className));
		$this->assertTrue($this->loader->isLoaded($className));
	}

	/**
	 * Test register unregister
	 * These methods wrap the spl_autoload_register/spl_autoload_unregister
	 * functions
	 *
	 * Assert: 	register adds array of object,method name onto the the
	 * 			existing methods AND that object is an Autoloader AND method
	 * 			is the same method name from getRegisteredMethodName
	 * Assert: 	unregister removes that entry
	 */
	public function testRegisterUnRegister()
	{
		$alFunctions = spl_autoload_functions();
		$this->assertTrue($this->loader->register());
		$alFunctions = spl_autoload_functions();
		$total = count($alFunctions);

		$result = end($alFunctions);
		$this->assertTrue(is_array($result));
		$this->assertArrayHasKey(0, $result);
		$this->assertArrayHasKey(1, $result);
		$this->assertTrue($result[0] instanceof Autoloader);
		$this->assertEquals(
			$this->loader->getRegisteredMethod(),
			$result[1]
		);

		$this->assertTrue($this->loader->unregister());
		$this->assertEquals($total - 1, count(spl_autoload_functions()));
	}
}

