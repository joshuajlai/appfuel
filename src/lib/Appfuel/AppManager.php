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

use Appfuel\Framework\Initializer,
	Appfuel\Framework\InitializerInterface,
	Appfuel\Framework\AppFactoryInterface,
	Appfuel\Framework\AppFactory;

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
	 * Used to put the framework in a known state, effects errors, 
	 * include path and autoloading
	 * @var Framework\Initializer
	 */
	static protected $initializer = NULL;

	/**
	 * Factory class used to create objects needed in Initialization, 
	 * Bootstrapping and Dispatching
	 * @var	Framework\App\FactoryInterface
	 */
	static protected $appFactory = NULL;

	/**
	 * Puts the framework into a known state
	 */
	static public function initialize($basePath, $file)
	{
		self::setBasePath($basePath);
		if (! self::isDependenciesLoaded()) {
			self::loadDependencies($basePath);		
		}

		if (self::isInitializer()) {
			$initializer = self::getInitializer();
		} else {
			$initializer = self::createInitializer($basePath);
			self::setInitializer($initializer);
		}

		$initializer->initialize($file);
	
		/*
		 * The initializer is responsible for the app factory because
		 * you can specify an alternate factory in the config and the 
		 * initializer controls the processing of the config
		 */
		self::setAppFactory($initializer->getFactory());

		/*
		 * During the app install a master ini file which contains sections 
		 * for each environment is reduced to the environment the app is
		 * is installed on. The install then puts the env name in the config
		 * for which we use to bootstrap the framework
		 */
		$envName = Registry::get('env', FALSE);
		if (! $envName) {
			throw new Exception('Initialize error: env not found in Registry');
		}
		self::setEnvName($envName);
	}

	/**
	 * @param	MessageInterface $msg
	 * @return	MessageInterface
	 */
	static public function bootstrap(MessageInterface $msg = NULL)
	{
		if (NULL === $msg) {
			$msg = self::createMessage();
		}

		$env       = self::getEnvName();
		$factory   = self::getFactory();
		$bootstrap = $factory->createBootstrap($env); 

		return $msg;
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
	static public function setAppFactory(AppFactoryInterface $factory)
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
		return self::$appFactory instanceof AppFactoryInterface;
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
	static protected function setBasePath($path)
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
	static public function createInitializer($basePath)
	{
		return new Initializer($basePath);
	}

	/**
	 * @return Framework\Initializer
	 */
	static public function getInitializer()
	{
		return self::$initializer;
	}

	/**
	 * @param 	Framework\Initializer
	 * @return	null	
	 */
	static public function setInitializer()
	{
		return self::$initializer;
	}

	/**
	 * @return bool
	 */
	static public function isInitializer()
	{
		self::$initializer instanceof InitializerInterface;
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
	static public function loadDependencies($basePath)
	{
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
