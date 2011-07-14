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
namespace Test;

use PHPUnit_Extensions_OutputTestCase as ParentTestCase,
	Appfuel\Stdlib\Filesystem\Manager as FileManager,
	Appfuel\App\Manager				  as AppManager;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class AfTestCase extends ParentTestCase
{
	/**
	 * Holds the original include path when the system starts up
	 * @var string
	 */
	static protected $originalIncludePath = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	private $afBasePath = AF_BASE_PATH;

	/**
	 * Used to backup the current state of autoloaders
	 * @var array
	 */
	protected $bkAutoloadFunctions = array();

	/**
	 * Used to backup the include path
	 * @var string
	 */
	protected $bkIncludePath = NULL;

	/**
	 * @return string
	 */
	static public function getOriginalIncludePath()
	{
		return self::$originalIncludePath;
	}

	/**
	 * @param	string	$path
	 * @return	null
	 */
	static public function setOriginalIncludePath($path)
	{
		self::$originalIncludePath = $path;
	}
	
	/**
	 * @return null
	 */
	public function getBasePath()
	{
		return $this->afBasePath;
	}

	/**
	 * @return null
	 */
	public function getTestBase()
	{
		return $this->afBasePath . DIRECTORY_SEPARATOR . 'test';
	}

	/**
	 * The path to test main namespace Test
	 *
	 * @return	string
	 */
	public function getTestNamespacePath()
	{
		return $this->getTestBase() . DIRECTORY_SEPARATOR . 'lib';
	}

	/**
	 * @return string
	 */
	public function getTestConfigFile()
	{
		return $this->getTestBase() . DIRECTORY_SEPARATOR .
			   'config'             . DIRECTORY_SEPARATOR .
			   'test.ini';
	}

	/**
	 * @return AfTestCase
	 */
	public function backupAutoloaders()
	{
		$this->bkAutoloadFunctions = spl_autoload_functions();
		return $this;
	}

	/**
	 * Remove registered autoloader functions. Note that this does not
	 * backup those functions
	 *
	 * @return AfTestCase
	 */
	public function clearAutoloaders()
	{
		$functions = spl_autoload_functions();
		foreach ($functions as $item) {
			if (is_string($item)) {
				spl_autoload_unregister($item);
			} else if (is_array($item) && 2 === count($item)) {
				spl_autoload_unregister(array($item[0], $item[1]));
			}

		}

	}
	
	/**
	 * Restore all settings from the config file. The original include path 
	 * that was used to initialize app fuel is also restored prior to reset.
	 * 
	 * @return null
	 */
	public function restoreAppfuelSettings()
	{
		set_include_path(self::getOriginalIncludePath());
		AppManager::initialize(
			$this->getBasePath(), 
			$this->getTestConfigFile()
		);	
	}

	/**
	 * @return array
	 */
	public function getBackedUpAutoloaders()
	{
		return $this->bkAutoloadFunctions;
	}

	/**
	 * @return AfTestCase
	 */
	public function restoreAutoloaders()
	{
		$functions = $this->getBackedUpAutoloaders();
		foreach ($functions as $item) {
			if (is_string($item)) {
				spl_autoload_register($item);
			} else if (is_array($item) && 2 === count($item)) {
				spl_autoload_register(array($item[0], $item[1]));
			}
		}

		return $this;
	}

	/**
	 * @return AfTestCase
	 */
	public function backupIncludePath()
	{
		$this->bkIncludePath = get_include_path();
		return $this;
	}

	/**
	 * @return bool
	 */
	public function restoreIncludePath()
	{
		$path = $this->getBackedUpIncludePath();
		if (! is_string($path) || empty($path)) {
			return  FALSE;
		}

		set_include_path($path);
		return TRUE;
	}

	/**
	 * @return string
	 */
	public function getBackedUpIncludePath()
	{
		return $this->bkIncludePath;
	}

	/**
	 * @return string
	 */
	public function getCurrentPath($relPath = NULL)
	{
		$class = get_class($this);
		$testPath = $this->getTestNamespacePath();
		$dir  = FileManager::classNameToDir($class);

		$full = $testPath . DIRECTORY_SEPARATOR . $dir;
		if (NULL !== $relPath && is_string($relPath) && ! empty($relPath)) {
			$full .= DIRECTORY_SEPARATOR . $relPath;
		}
		return $full;
	}

	/**
	 * Provides a mock route so you don't have to specify the all the methods
	 * 
	 * @return	RouteInteface
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
        $route = $this->getMockBuilder($routeInterface)
                      ->setMethods($methods)
                      ->getMock();

		return $route;
	}

	/**
	 * @return	MessageInteface
	 */
	public function getMockMessage()
	{
        /* namespace to the known action controller */
        $msgInterface = 'Appfuel\Framework\App\MessageInterface';
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
	 * @return	ControllerInteface
	 */
	public function getMockActionController()
	{
        /* namespace to the known action controller */
        $interface = 'Appfuel\Framework\App\Action\ControllerInterface';
        $methods = array(
            'addSupportedDocs',
            'addSupportedDoc',
			'getSupportedDocs',
            'isSupportedDoc',
			'initialize',
			'execute'
        );

        return $this->getMockBuilder($interface)
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
	public function createMockClientsideFile($path)
	{
        $path = $this->getCurrentPath($path);
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
