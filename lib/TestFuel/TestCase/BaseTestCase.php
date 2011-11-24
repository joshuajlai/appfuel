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

use StdClass,
	Appfuel\App\AppManager,
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
	 * Absolute path to the test directory
	 * @var string
	 */
	static protected $testPath = null;

	/**
	 * Absolute path to the test files directory
	 * @var string
	 */
	static protected $testFilesPath = null;

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
	 * Backup of the registry just after intialization
	 * @var	array
	 */
	static protected $registryData = null;

	/**
	 * Backup of the domain map
	 * @var	array
	 */
	static protected $domainMap = null;

	/**
	 * @param	AppManager	$manager
	 * @param	string		$configFile		absolute path to config file
	 * @return	null
	 */
	static public function initialize($base)
	{
		$file = "{$base}/lib/Appfuel/App/AppManager.php";
		if (! file_exists($file)) {
			throw new \Exception("Could not find app manager file at $file");
		}
		require_once $file;

		$manager = new AppManager($base, 'app-test');
		$manager->initialize('test');

		self::$registryData = Registry::getAll();
		self::$domainMap	= Registry::getDomainMap();

		self::$envState = new EnvState();
		
		$test = "$base/test";
		self::$basePath			= $base;
		self::$appManager		= $manager;
		self::$testPath			= $test;
		self::$testFilesPath	= "{$test}/files";
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
	public function getTestFilesPath()
	{
		return self::$testFilesPath;
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

	/**
	 * @return	array
	 */
	public function provideInvalidStringsIncludeNull()
	{
		$data = $this->provideInvalidStrings();
		array_push($data, array(null));
		return $data;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(0), 
			array(-1), 
			array(1), 
			array(12345), 
			array(1.23454), 
			array(true), 
			array(false), 
			array(new StdClass()),
			array(array()), 
			array(array(1)),
			array(array(1,2,3)),
		);
	}

	/**
	 * Restore the registry to a state it was when we initialized
	 *
	 * @return	null
	 */
	public function restoreRegistry()
	{
		$this->initializeRegistry(
			self::$registryData,
			self::$domainMap
		);
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	public function initializeRegistry($data = null, array $domainMap = null)
	{
		if (null === $data) {
			$data = self::$registryData;
		}
		Registry::initialize($data, $domainMap);
	}

    /**
     * Provides a mock route so you don't have to specify the all the methods
     * 
     * @return  RouteInteface
     */
    public function getMockRoute()
    {  
        /* namespace to the known action controller */
        $routeInterface = 'Appfuel\Framework\App\Route\RouteInterface';
        $methods = array(
            'getRouteString',
            'getAccessPolicy',
            'getResponseType',
            'getActionNamespace',
            'getSubModuleNamespace',
            'getModuleNamespace',
            'getRootActionNamespace'
        );
    
		return $this->getMockBuilder($routeInterface)
                    ->setMethods($methods)
                    ->getMock();
    }

    /**
     * @return  MessageInteface
     */
    public function getMockContext()
    {
        /* namespace to the known action controller */
        $msgInterface = 'Appfuel\Framework\App\ContextInterface';
        $methods = array(
            'getRoute',
            'SetRoute',
            'isRoute',
            'getRequest',
            'setRequest',
            'isRequest',
            'getResponseType',
            'setResponseType',
            'calculateResponseType',
            'getError',
            'setError',
            'isError',
            'clearError',
            /* dictionary methods */
            'add',
            'get',
            'getAll',
            'count',
            'load'
        );

        return $this->getMockBuilder($msgInterface)
                    ->setMethods($methods)
                    ->getMock();
    }

    /**
     * Used to encapsulate the common logic necessary for testing
     * the template builds
     *
     * @param   string  $path
     * @return  Appfuel\Framework\FileInterface
     */
    public function createMockFrameworkFile($path)
    {
        $path = "{$this->getTestFilesPath()}/{$path}";
        $file = $this->getMock('Appfuel\Framework\File\FrameworkFileInterface');

        $file->expects($this->any())
             ->method('isFile')
             ->will($this->returnValue(true));

        $file->expects($this->any())
             ->method('getRealPath')
             ->will($this->returnValue($path));

        $file->expects($this->any())
             ->method('getFullPath')
             ->will($this->returnValue($path));

        return $file;
    }
}
