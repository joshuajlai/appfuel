<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel;

/**
 * The AppManager is used to encapsulate the logic need to build an App object,
 * the application object used to run a user request. Because This is the fist
 * file the calling code is likely to use it will not be governed by an 
 * interface. It will also hold the responsibility of initializing the system.
 */
class AppManager
{
	/**
	 * Flag to determine if dependencies have been loaded
	 * @var bool
	 */
	static protected $isLoaded = FALSE;

	/**
	 * Root path of the application
	 * @var string
	 */
	static protected $basePath = NULL;

	/**
	 * Factory class used to create objects needed in Initialization, 
	 * Bootstrapping and Dispatching
	 * @var	Framework\AppFactoryInterface
	 */
	protected $appFactory = NULL;

	/**
	 * Store name value pairs in this simple registry
	 * @var array
	 */
	static protected $data = NULL;

	/**
	 * 
	 */
	public function init($basePath, $configFile = NULL)
	{
		self::setBasePath($basePath);

		if (! defined('AF_BASE_PATH')) {
			define('AF_BASE_PATH', $basePath);
		}
		
		self::loadDependencies($basePath);		

		$ini = self::getConfigData($configFile);
		if (! is_array($ini)) {
			return;
		}
		self::setRegistry($ini);

		$iPaths  = self::getRegistryItem('include_path', '');
		$iAction = self::getRegistryItem('include_path_action', 'replace');
		self::initIncludePath($iPaths, $iAction);
		
		$autoload = self::getRegistryItem('autoload_class', NULL);
		self::initAutoload($autoload);

		$errors = self::getRegistryItem('errors', array());
		self::initErrorSettings($errors);

		echo "\n", print_r($errors,1), "\n";exit; 
	}
	
	/**
	 * Parse the ini file given. When the parameter is empty use the
	 * default location
	 *
	 * @param	string	$configFile		path the ini file
	 * @param	bool	$useBase		use base path to resolve absolute path
	 * @return	mixed	FALSE|array
	 */
	static public function getConfigData($file = NULL, $useBase = TRUE)
	{
		if (NULL === $file) {
			$file = $this->getDefaultConfigPath();
		}

		if (TRUE === $useBase) {
			$file = self::getBasePath() . DIRECTORY_SEPARATOR . $file;
		}

		if (! file_exists($file)) {
			throw new Exception("Could not find config file ($file)");
		}


		return Stdlib\Filesystem\Manager::parseIni($file);
	}

	/**
	 * Initialize the php include path. Handles a single string or an
	 * array of strings. The action parameter is used to determine how
	 * how to deal with the original include path. should we append, prepend,
	 * or replace it
	 * 
	 * @param	mixed	$paths
	 * @param	string	$action		how to deal with the original path
	 * @return	NULL	
	 */
	public function initIncludePath($paths, $action = 'replace')
	{
        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $pathString = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $pathString = implode(PATH_SEPARATOR, $paths);
        } else {
            return FALSE;
        }

        /*
         * The default action is to replace the include path. If
         * action is given with either append or prepend the 
         * paths will be concatenated accordingly
         */
        $includePath = get_include_path();
        if ('append' === $action) {
            $pathString = $includePath . PATH_SEPARATOR . $pathString;
        } else if ('prepend' === $action) {
            $pathString .= PATH_SEPARATOR . $includePath;
        }

        return set_include_path($pathString);
	}

	/**
	 * Initalize the autoload class given. If no class is given use
	 * the appfuel autoloader. 
	 *
	 * @param	string	$class
	 * @return	NULL
	 */
	public function initAutoload($class = NULL)
	{
		if (empty($class) || empty($path)) {
			$autoloader = new Framework\Autoloader();
		} else {
			$file = Stdlib\Filesystem\Manager::classNameToFileName($class);
			$file = self::getBasePath() . DIRECTORY_SEPARATOR . 
					'lib'               . DIRECTORY_SEPARATOR .
					$file;
			if (! file_exists($file)) {
				throw new Exception("Could not find autoloader class ($file)");
			}
			require_once $file;

			$autoloader = new $class();
		}

		$type = '\Appfuel\Framework\AutoloadInterface';
		if (! is_a($autoloader, $type)) {
			throw new Exception("Autoloader must implement $type");
		}

		$autoloader->register();
	}

	/**
	 * @return	string
	 */
	static public function getBasePath()
	{
		return self::$basePath;
	}
	
	/**
	 * @return string
	 */
	static public function getDefaultConfigPath()
	{
		return 'config' . DIRECTORY_SEPARATOR . 'app.ini';
	}

	/**
	 * @param	string	$path
	 * @return	NULL
	 */
	protected function setBasePath($path)
	{
		if (empty($path) || ! is_string($path)) {
			throw new \Exception("Param Error: Base path must be a string");
		}
		
		self::$basePath = $path;
	}

	/**
	 * @return bool
	 */
	static public function isDependenciesLoaded()
	{
		return self::$isLoaded;
	}

	/**
	 * Resolves the path to the Dependecy class and loads app fuel dependent
	 * files. Note that these files are located at the lib directory off the
	 * base directory
	 *
	 * @param	string	$basePath
	 * @return	NULL
	 */
	public function loadDependencies($basePath)
	{
		if (self::isDependenciesLoaded()) {
			return;
		}

		$path = $basePath . DIRECTORY_SEPARATOR . 'lib';
		$file = $path     . DIRECTORY_SEPARATOR . 
				'Appfuel' . DIRECTORY_SEPARATOR . 'Dependency.php';

		if (! file_exists($file)) {
			throw new \Exception("Dependency file could not be found ($file)");
		}

		require_once $file;

		$depend = new Dependency($path);
		$depend->load();

		self::$isLoaded = TRUE;
	}

	/**
	 * @param	array	$data
	 * @return	NULL
	 */
	static public function setRegistry(array $data)
	{
		self::$data = $data;
	}

	/**
	 * @return array
	 */
	static public function getRegistry()
	{
		return self::$data;
	}

	/**
	 * Merge data into the registry
	 *
	 * @param	array $data
	 * @return	NULL
	 */
	static public function loadRegistry(array $data)
	{
		self::$data = array_merge(self::$data, $data);
	}

	/**
	 * Get an item out of the registry
	 *
	 * @param	string	$name
	 * @param	mixed	$default	return when not found
	 * @return	mixed
	 */
	static public function getRegistryItem($name, $default = NULL)
	{
		if (! is_string($name) || empty($name)) {
			return $default;
		}

		if (! array_key_exists($name, self::$data)) {
			return $default;
		}

		return self::$data[$name];
	}
}
