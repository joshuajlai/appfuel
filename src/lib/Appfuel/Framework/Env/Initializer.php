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
	 * Parse the config file into an array and use that to initialize a central
	 * registry then use the registry to initialize the framework. The config 
	 * file must exist or an exception is thrown.
	 *
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	static public function initialize($basePath, $file)
	{
		$data = self::getConfigData($file);
		if (! $data) {
			$data = array();
		}

		/*
		 * The registry is central for allowing the system to share 
		 * configuration information. I try to use it only within the framework
		 * startup (initalization and bootstrapping) after that you have 
		 * enough tools to create the appropriate object relationships
		 */
		self::initRegistry($data);

		/*
		 * Initializing from the registry allows us to use the Registry 
		 * interface to easily collect togather config data that may or may 
		 * not be there. No need to check array indexes
		 */
		return self::initFromRegistry();
	}
	
	/**
	 * Use the data in the registry to determine if we should initialize the
	 * following config keys:	display errors
	 *							error reporting level
	 *							include path
	 *							default timezone
	 *							autoloader
	 *
	 * @return	null
     */
	static public function initFromRegistry()
	{
		$display = Registry::get('display_errors', null);
		if (null !== $display) {
			$errorDisplay = AppFactory::createErrorDisplay();
			$errorDisplay->set($display);
		}

		$errorLevel = Registry::get('error_reporting', null);
		if (null !== $errorLevel) {
			$errorReporting = AppFactory::createErrorReporting();
			$errorReporting->setLevel($errorLevel);
		}

		$ipath   = Registry::get('include_path', null);
		$iaction = Registry::get('include_path_action', null);
		if (null !== $ipath) {
			$includePath = AppFactory::createIncludePath();
			$includePath->usePaths($ipath, $iaction);
		}

		$defaultTz = Registry::get('default_timezone', null);
		if (null !== $defaultTz) {
			$timezone = AppFactory::createTimezone();
			$timezone->setDefault($defaultTz);

		}
		
		$enableAutoloader =(bool) Registry::get('enable_autoloader', null);
		if (true === $enableAutoloader) {
			$autoloader = AppFactory::createAutoloader();
			$autoloader->register();
		}
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
