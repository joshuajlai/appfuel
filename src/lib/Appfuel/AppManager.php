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

use Appfuel\Framework\App\Initializer,
	Appfuel\Framework\App\FactoryInterface,
	Appfuel\Framework\App\Factory as AppFactory;

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
	 * Name of the environment the server is deployed
	 * @var	string
	 */
	static protected $env = NULL;


	/**
	 * Factory class used to create objects needed in Initialization, 
	 * Bootstrapping and Dispatching
	 * @var	Framework\App\FactoryInterface
	 */
	static protected $appFactory = NULL;

	/**
	 * 
	 */
	public function initialize($basePath, $file)
	{
		self::setBasePath($basePath);
		self::loadDependencies($basePath);		
		$initializer = self::createInitializer($basePath);
		$initializer->initialize($file);
	
		self::setAppFactory($initializer->getFactory());

		$envName = Registry::get('env', FALSE);
		if (! $envName) {
			throw new Exception('Initialize error: env not found in Registry');
		}
		self::setEnvName($envName);
	}

	/**
	 * @return	Framework\App\FactoryInterface
	 */
	static public function getAppFactory()
	{
		return self::$appFactory;
	}

	/**
	 * @return	NULL
	 */
	static public function setAppFactory(FactoryInterface $factory)
	{
		return self::$appFactory = $factory;
	}

	/**
	 * @return	NULL
	 */
	static public function clearAppFactory()
	{
		self::$appFactory = NULL;
	}

	/**
	 * @return	Framework\App\FactoryInterface
	 */
	static public function isAppFactory()
	{
		return self::$appFactory instanceof FactoryInterface;
	}

	/**
	 * @return	string
	 */
	static public function getEnvName()
	{
		return self::$env;
	}

	/**
	 * @param	string
	 * @return	NULL
	 */
	static public function setEnvName($name)
	{
		self::$env = $name;
	}

	/**
	 * @return	string
	 */
	static public function getBasePath()
	{
		return self::$basePath;
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
	
		if (! defined('AF_BASE_PATH')) {
			define('AF_BASE_PATH', $path);
		}

		self::$basePath = AF_BASE_PATH;
	}

	/**
	 * @param	string	$basePath
	 * @return	Initializer
	 */
	public function createInitializer($basePath)
	{
		return new Initializer($basePath);
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
}
