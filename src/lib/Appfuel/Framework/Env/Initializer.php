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
namespace Appfuel\Framework\Env;

use Appfuel\Framework\AppFactory,
	Appfuel\Framework\Exception,
	Appfuel\Registry,
	Appfuel\Stdlib\Filesystem\Manager	as FileManager;

/**
 * The Initializer is used to put the framework into a known state for the
 * the following areas: include path, config data, error display, error
 * reporting and class autoloading.
 */
class Initializer
{
    /**  
	 * Parse the config file into an array and use that array to initalize
	 * (load) the Registry. Then use the registry to put the framework into
	 * a known state
	 *
     * @param   string  $file	file path to config ini
	 * @return	Appfuel\Framework\AppFactoryInterface
     */
	static public function initialize($basePath, $file)
	{
		$data = self::getConfigData($file);
		if (! $data) {
			$data = array();
		}
		self::initRegistry($data);
		self::initFromRegistry();
	}
	
	/**
	 * Use the data in the registry to determine if we should initialize the
	 * following :	include path, 
	 *				php errors	(display_errors, error_reporting)
	 *				autoloader
	 *				default timezone
	 *
     * @param   string  $file	file path to config ini
	 * @return	null
     */
	static public function initFromRegistry()
	{
		$keys = array(
			'include_path',
			'include_path_action', 
			'display_errors', 
			'error_reporting',
			'enable_autoloader',
			'default_timezone'
		);

		$data = Registry::collect($keys);
		$state = AppFactory::createEnvState($data);
		self::initState($state);
		return $state;
	}
	
	/**
	 * Initialize the enviroment based on the state settings
	 * 
	 * @param	State	$state
	 * @return	null
	 */
	public function initState(State $state)
	{
		if ($state->isErrorConfiguration()) {
			$errDisplay = $state->displayErrors();
			$errReport  = $state->errorReporting();
			self::initPHPError($errDisplay, $errReport);
		}

		if ($state->isIncludePathConfiguration()) {
			$path   = $state->includePath();
			$action = $state->includePathAction();
			self::initIncludePath($path, $action);
		}

		if ($state->isTimezoneConfiguration()) {
			self::initDefaultTimezone($state->defaultTimezone());
		}

		if ($state->isRestoreAutoloaders()) {
			$loaders = $state->autoloaders();
			self::restoreAutoloaders($loaders);
		} 
		else if ($state->isEnableAutoloader()) {
			self::initAutoloader();
		}
	}

	/**
	 * Initialize the php include path. Action to be performed against the
	 * the path include: replacing the include path, appending to the path or
	 * prepending to the path
	 * 
	 * @param	array	$paths
	 * @param	string	$action
	 * @return	string  the previous include path
	 */
	static public function initIncludePath(array $paths, $action = 'replace')
	{
		$includePath = AppFactory::createIncludePath();
		return $includePath->usePaths($paths, $action);
	}

	/**
	 * Initialize the way php displays and reports errors.
	 *
	 * @param	string	$display
	 * @param	string	$reporting
	 * @return	null
	 */
	static public function initPHPError($display = NULL, $reporting = NULL)
	{
		$error = AppFactory::createPHPError();
		
		if (! empty($display)) {
			$error->setDisplayStatus($display);
		}

		if (! empty($reporting)) {
			$error->setReportingLevel($reporting);
		}
	}

	/**
	 * Register the frameworks autoloader
	 * 
	 * @return null
	 */
	static public function initAutoloader()
	{
		$autoloader = AppFactory::createAutoloader();
		$autoloader->register();
	}

	/**
	 * set the default timezone the framework will use
	 *
	 * @param	string	$timezone
	 * @return	bool
	 */
	static public function initTimezone($timezone)
	{
		$timezone = AppFactory::createTimezone();
		return $timezone->setDefault($timezone);
	}

	/**
	 * Initialize the Appfuel\Registry with or without data
	 *
	 * @param	array	$data
	 * @return	NULL
	 */
	static public function initRegistry(array $data = array())
	{
		Registry::init($data);
	}

    /**
     * Parse the ini file given into an associative array
     *
	 * @throw	Exception	when file is not found	
     * @param   string	$configFile		path the ini file
     * @return  mixed	false|array
     */
	static public function getConfigData($file)
	{
        if (! file_exists($file)) {
            throw new Exception("Could not find config file ($file)");
        }

        return FileManager::parseIni($file);
	}
}
