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

use Appfuel\Framework\Env\Initializer,
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
	static protected $envName = NULL;

    /**
     * Flag used to determine if the framework has been initialized
     * @return bool
     */
    static protected $isInitialized = FALSE;

    /**
     * Type of application being started Cli, Web, Api
     * @var string
     */
    static protected $type = NULL;

	/**
	 * Puts the framework into a known state
	 */
	static public function initialize($basePath, $file)
	{
		self::setBasePath($basePath);
		if (! self::isDependenciesLoaded()) {
			self::loadDependencies($basePath);		
		}

		Initializer::initialize($basePath, $file);
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
	static public function startUp($type, MessageInterface $msg = NULL)
	{
		self::validateInitialization();
		if (NULL === $msg) {
			$msg = self::createMessage();
		}

		$env       = self::getEnvName();
		$factory   = self::getFactory();
		$startup   = $factory->createStartup($type); 
		$params    = $msg->get('startupParams', array());

		$uri = $factory->createUri($startup->getUriString());
		$request = $factory->createRequest($uri, $startup->getRequestParams());
	
		$response = DomainHandler::getData(
			'Appfuel\\Domain\\Route',
			'Find',
			array('routeString' => $request->getRouteString())
		);

		$msg->setRequest($request)
			->setRoute($route);

		return $startup->bootstrap($msg);;
	}

	static public function dispatch(MessageInterface $msg)
	{
		self::validateInitialization();

	}

	static public function render(MessageInterface $msg)
	{
		self::validateInitialization();

	}

    /**
     * @param   mixed   $data
     * @return  Message
     */
	static public function createMessage($data = NULL)
	{
		self::validateInitialization();
        return self::getAppFactory()->createMessage($data);
	}

	/**
	 * @return	string
	 */
	static public function getEnvName()
	{
		return self::$envName;
	}

	/**
	 * @param	string
	 * @return	NULL
	 */
	static public function setEnvName($name)
	{
		self::$envName = $name;
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
     * Has the system been initialized through the initializer
     *
     * @return  bool
     */
    static public function isInitialized()
    {
        return self::$isInitialized;
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
