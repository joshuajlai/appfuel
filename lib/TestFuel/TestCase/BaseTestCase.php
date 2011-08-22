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
namespace TestFuel\TestCase;

use Appfuel\App\AppManager,
	Appfuel\Framework\Registry,
	Appfuel\Framework\Env\State as EnvState,
	Appfuel\Framework\File\FileManager,
	PHPUnit_Extensions_OutputTestCase;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class BaseTestCase extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * Absolute path to the test config file
	 * @var string
	 */
	static protected $testConfigFile = null;

	/**
	 * Absolute path to the test directory
	 * @var string
	 */
	static protected $testPath = null;

	/**
	 * Absolute path to the application root directory. We cache it here, to
	 * avoid calling the AppManager over and over again
	 * @var string
	 */
	static protected $basePath = null;

	/**
	 * Absolute path to the parent directory of the appfuel test cases
	 * @var string
	 */
	static protected $appfuelTestPath = null;

	/**
	 * The original includepath used before initialization 
	 * @var string
	 */
	static protected $originalIncludePath = null;

	/**
	 * AppManager is used to initialize the system. 
	 * @var AppManager
	 */
	static protected $appManager = null;
	
	/**
	 * value object that contains values for error display, reporting,
	 * default timezone and autoload stack
	 * @var EnvState
	 */
	static protected $envState = null;

	/**
	 * @param	AppManager	$manager
	 * @param	string		$configFile		absolute path to config file
	 * @return	null
	 */
	static public function initialize($base, $configFile)
	{
		$file = "{$base}/lib/Appfuel/App/AppManager.php";
		if (! file_exists($file)) {
			throw new \Exception("Could not find app manager file at $file");
		}
		require_once $file;

		self::$originalIncludePath = get_include_path();
		$manager = new AppManager($base, $configFile);
		$manager->initialize();


		self::$envState = new EnvState();
		
		$test = "$base/test";
		self::$basePath			= $base;
		self::$appManager		= $manager;
		self::$testConfigFile	= $configFile;
		self::$testPath			= $test;
		self::$appfuelTestPath  = "{$test}/appfuel";
	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return self::$basePath;
	}

	/**
	 * @return	string
	 */
	public function getTestPath()
	{
		return self::$testPath;
	}

	/**
	 * @return	string
	 */
	public function getAppfuelTestPath()
	{
		return self::$appfuelTestPath;
	}

	/**
	 * @return	string
	 */
	public function getTestConfigFile()
	{
		return self::$testConfigFile;
	}

	/**
	 * @return	AppManager
	 */
	public function getAppManager()
	{
		return self::$appManager;
	}

	/**
	 * @return	EnvState
	 */
	public function getEnvState()
	{
		return	self::$envState;
	}
}
