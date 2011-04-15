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

use Appfuel\Framework\Env\Autoloader,
	Appfuel\Framework\Env\ErrorReporting,
	Appfuel\Framework\Env\ErrorDisplay,
	Appfuel\Framework\Env\TimeZone,
	Appfuel\Framework\Env\IncludePath;

/**
 * Responsible for creating objects required by the framework for 
 * initializaion, bootstrapping, dispatching and outputting.
 */
class Factory
{
	/**
	 * Used to register spl autoloader for the framework
	 *
	 * @return	Autoloader
	 */
	static public function createAutoloader()
	{
		return new Autoloader();
	}

	/**
	 * @return	ErrorReporting
	 */
	static public function createErrorReporting()
	{
		return new ErrorReporting();
	}

	/**
	 * @return	ErrorDisplay
	 */
	static public function createErrorDisplay()
	{
		return new ErrorDisplay();
	}



	/**
	 * Used to change the php include_path
	 *
	 * @return IncludePath
	 */
	static public function createIncludePath()
	{
		return new IncludePath();
	}
	
	/**
	 * @return Timezone
	 */
	static public function createTimezone()
	{
		return new Timezone();
	}

    public function createBootstrapper($type)
	{
	
	}

    public function createStartupStrategy($type)
	{

	}

    public function createFrontController()
	{

	}

    public function createDispatcher()
	{
	
	}

    public function createOutputEngine()
	{

	}
}
