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
 * The AppBuilder is used to encapsulate the logic need to build an App object,
 * the application object used to run a user request. Because This is the fist
 * file the calling code is likely to use it will not be governed by an 
 * interface. It will also hold the responsibility of initializing the system.
 */
class AppBuilder
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
	protected $basePath = NULL;

	/**
	 * Factory class used to create objects needed in Initialization, 
	 * Bootstrapping and Dispatching
	 * @var	Framework\AppFactoryInterface
	 */
	protected $appFactory = NULL;

	/**
	 * Config file used when no file is given
	 * @var string
	 */
	protected $defaultConfig = NULL;

	/**
	 * @param	string	$path 
	 * @return	AppBuilder
	 */
	public function __construct($path)
	{
		$this->setBasePath($path);

		$this->loadDependencies($path);		
		if (! defined('AF_BASE_PATH')) {
			define('AF_BASE_PATH', $path);
		}

		$this->defaultConfig = $path    . DIRECTORY_SEPARATOR .
							   'config' . DIRECTORY_SEPARATOR .
							   'app.ini';
	}

	/**
	 * 
	 */
	public function init($configFile = NULL)
	{
		if (NULL === $configFile) {
			$configFile = $this->getDefaultConfigPath();
		}

		if (! file_exists($configFile)) {
			throw new Exception("Could not find config file ($configFile)");
		}

		$ini = Stdlib\Filesystem\Manager::parseIni($configFile);
		if (isset($ini['include_path']) && ! empty($ini['include_path'])) {
			$action = NULL;
			if (isset($ini['include_path_action'])) {
				$action = $ini['include_path_action'];
			}
			$this->initIncludePath($ini['include_path'], $action);
		}

		
		
		echo "\n", print_r(get_include_path(),1), "\n";exit; 
	}

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
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultConfigPath()
	{
		return $this->defaultConfig;
	}

	/**
	 * @param	string	$path
	 * @return	AppBuilder
	 */
	protected function setBasePath($path)
	{
		$errMsg = "Base path must be a string";
		$this->basePath = $this->validateString($path, $errMsg);
		return $this;
	}

	/**
	 * @param	string	$string		string to be validated
	 * @param	string	$err		error message when not valid
	 * @return	string
	 */
	protected function validateString($string, $err)
	{
		if (empty($string) || ! is_string($string)) {
			throw new \Exception("Validation Error: $err");
		}
		
		return $string;
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
	protected function loadDependencies($basePath)
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
}
