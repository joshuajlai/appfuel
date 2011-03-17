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
namespace Appfuel\Framework;

use Appfuel\Stdlib\Filesystem\Manager	as FileManager,
	Appfuel\Registry,
	Appfuel\AppManager;

/**
 * The Initializer is used to put the framework into a known state for the
 * the following areas: include path, config data, error display, error
 * reporting and class autoloading.
 */
class Initializer implements InitializeInterface
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

    /**
     * Used to map and change the php error display_errors and error_reporting
     * @var Stdlib\Error\PHPError
     */
    protected $phpError = NULL;

    /**
     * Autoloader use is loading classes into memory
     * @var Framework\Autoload\AutoloadInterface
     */
    protected $autoloader = NULL;

	/**
	 * Creates objects necessary to Initialize, Bootstrap, Dispatch, and Output
	 * @var FactoryInterface
	 */
	protected $factory = NULL;

	/**
	 * @param	string	$basePath
	 * @return	Initializer
	 */
	public function __construct($basePath)
	{
		if (! is_string($basePath) || empty($basePath)) {
			throw new \Exception("Base path should be none empty string");
		}
		$this->basePath = $basePath;

	}

    /**  
	 * Parse the config file into an array and use that array to initalize
	 * (load) the Registry. Then use the registry to put the framework into
	 * a known state
	 *
     * @param   string  $file	file path to config ini
	 * @return	NULL
     */
	public function initialize($file)
	{
		$this->initRegistryWithConfig($file);
		$this->initFromRegistry();
	}
	
	/**
	 * 1) pull out the app factory class and create it then 
	 *	  assign the PHPError and Autoloader classes 
     * 2) initialize the include path
     * 3) initialize error settings
     * 4) register the autoloader
	 *
	 * We set the Factory, PHPError and Error so the Manager can have access
	 * to them through this object
	 *
     * @param   string  $file	file path to config ini
	 * @return	NULL
     */
	public function initFromRegistry()
	{
		
		$factory = $this->getFactory();
		if (! $factory instanceof AppFactoryInterface) {
			$defaultFactory = '\Appfuel\Framework\AppFactory';
			$fClass  = Registry::get('app_factory', $defaultFactory);
			$factory = $this->createFactory($fClass);
			$this->setFactory($factory);
		}

		$paths  = Registry::get('include_path', FALSE);
        $action = Registry::get('include_path_action', 'replace');
	
		if ($paths) {
			$this->includePath($paths, $action);
		}

		$error = $this->getPHPError();
		if (! $error instanceof PHPErrorInterface) {       
			$error   = $factory->createPHPError();
			$display = Registry::get('display_error',   'off');
			$level   = Registry::get('error_reporting', 'none');
			$this->setPHPError($error);
		}
		$error->setDisplayStatus($display);
        $error->setReportingLevel($level);
 	
		$autoloader = $this->getAutoloader();
		if (! $autoloader instanceof AutoloadInterface) {
			$autoloader = $factory->createAutoloader();
			$this->setAutoloader($autoloader);
		}
		$autoloader->register();
	}

	/**
	 * Convert ini file into an array of data and use that to initialize
	 * The application registry
	 *
	 * @param	string	$file	path to config file
	 * @return	NULL
	 */
	public function initRegistryWithConfig($file)
	{
		$data = $this->getConfigData($file);
		if (! is_array($data)) {
			$data = array();
		}
		$this->initRegistry($data);	
	}

	/**
	 * Initialize the Appfuel\Registry with or without data
	 *
	 * @param	array	$data
	 * @return	NULL
	 */
	public function initRegistry(array $data = array())
	{
		Registry::init($data);
	}

    /**
     * Parse the ini file given into an associative array
     *
	 * @throw	Exception	when file is not found	
     * @param   string	$configFile		path the ini file
     * @param   bool	$useBase		use base path to resolve absolute path
     * @return  mixed	FALSE|array
     */
	public function getConfigData($file)
	{
        $file = $this->getBasePath() . DIRECTORY_SEPARATOR . $file;
        if (! file_exists($file)) {
            throw new Exception("Could not find config file ($file)");
        }

        return FileManager::parseIni($file);
	}

    /**
     * Initialize the php include path. Handles a single string or an
     * array of strings. The action parameter is used to determine how
     * how to deal with the original include path. should we append, prepend,
     * or replace it
     * 
     * @param   mixed   $paths
     * @param   string  $action     how to deal with the original path
     * @return  NULL    
     */
    public function includePath($paths, $action = 'replace')
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
     * @return  FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return  FactoryInterface
     */
    public function setFactory(AppFactoryInterface $factory)
    {
		$this->factory = $factory;
        return $this;
    }

	/**
	 * Create the app factory by converting namespaces into directory paths
	 * locating the file adding it into memory then instantiating the class.
	 * This is done because it generally happens before the autoloader has
	 * been registered.
	 *
	 * @throws	\Exception	when the class file does not exist
	 * @param	string	classnName
	 * @return	FactoryInterface
	 */
	public function createFactory($className)
	{
		$root = $this->getBasePath() . DIRECTORY_SEPARATOR . 'lib';
		$path = FileManager::classNameToFileName($className);
		$file = $root . DIRECTORY_SEPARATOR . $path;
		if (! file_exists($file)) {
			throw new \Exception("could not find app factory file ($file)");
		}
		require_once $file;

		return new $className();
	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

    /**
     * @return  AutoloadInterface
     */
    public function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * @return  AutoloadInterface
     */
    public function setAutoloader(AutoloadInterface $autoloader)
    {
		$this->autoloader = $autoloader;
        return $this;
    }

    /**
     * @return  PHPErrorInterface
     */
    public function getPHPError()
    {
        return $this->phpError;
    }

	/**
	 * @param	PHPErrorInterface	$error
	 * @return	Initializer
	 */
	public function setPHPError(PHPErrorInterface $error)
	{
		$this->phpError = $error;
		return $this;
	}
}
