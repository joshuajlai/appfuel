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
namespace Appfuel\App;

use Appfuel\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\MessageInterface;

/**
 * The AppManager is used to encapsulate the logic need to build an App object,
 * the application object used to run a user request. Because This is the fist
 * file the calling code is likely to use it will not be governed by an 
 * interface. It will also hold the responsibility of initializing the system.
 */
class Manager
{
	/**
	 * Flag to determine if dependencies have been loaded
	 * @var bool
	 */
	static protected $isLoaded = FALSE;

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
	 * Initialization: This is the first phase in the an application request
	 * life cycle. In this phase we must put the framework into a known state.
	 * We use the config file given from which to initialize errors, timezone,
	 * autoloading, and include path. We also add all data in the config file
	 * into the application registry so the framework can have access. Finally,
	 * we check the env has been set and tell the manager we are initialized.
	 *
	 * @throw	Appfuel\Framework\Exception
	 *
	 * @param	string	$basePath	root path of the application
	 * @param	string	$file		path to the config file
	 * @return	null
	 */
	static public function initialize($basePath, $file)
	{
		/* only used to resolve the base path in any config files */
        if (! defined('AF_BASE_PATH')) {
            define('AF_BASE_PATH', $basePath);
        }

		if (! self::isDependenciesLoaded()) {
			self::loadDependencies($basePath);		
		}
		
		Registry::initialize(array('base_path' => $basePath));
		Initializer::initialize($file);
		
		/*
		 * During the app install a master ini file which contains sections 
		 * for each environment is reduced to the environment the app is
		 * is installed on. The install then puts the env name in the config
		 * for which we use to bootstrap the framework
		 */
		if (! Registry::exists('env')) {
			throw new Exception('Initialize error: env not found in Registry');
		}

		/* tell the manager we are initialized and ready */
		self::setInitializedFlag(true);
	}

    static public function run($basePath, $file, $type)
    {   
        self::initialize($basePath, $file);

        Registry::add('app_type', $type);
        $msg = Factory::createMessage();

        $msg = self::startUp($type, $msg);
        $msg = self::dispatch($msg);

        self::render($msg);
    }

	/**
	 * @param	MessageInterface $msg
	 * @return	MessageInterface
	 */
	static public function startUp($type, MessageInterface $msg = NULL)
	{
        if (! self::isInitialized()) {
            throw new Exception("Must initialize before startup");
        }

        //$bootstrap = Factory::createBootstrap($type);
        $request   = Factory::createRequest();

		$responseType = $request->get('responseType', 'get', 'html');
		        
		$route = Factory::createErrorRoute();
		echo "<pre>", print_r($route, 1), "</pre>";exit; 
		$msg->add('request', $request)
            ->add('responseType', $responseType)
			->add('route', $route);

		return $msg;
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
     * Has the system been initialized through the initializer
     *
     * @return  bool
     */
    static public function isInitialized()
    {
        return self::$isInitialized;
    }

	/**
	 * Inform the manager that all system initialization needed is completed
	 * 
	 * @param	bool	$flag
	 * @return	null
	 */
	static public function setInitializedFlag($flag)
	{
		self::$isInitialized =(bool) $flag;
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
				'Appfuel' . DIRECTORY_SEPARATOR . 
				'App'	  . DIRECTORY_SEPARATOR .
				'Dependency.php';

		if (! file_exists($file)) {
			throw new \Exception("Dependency file could not be found ($file)");
		}

		require_once $file;

		$depend = new Dependency($path);
		$depend->load();

		self::$isLoaded = TRUE;
	}
}
