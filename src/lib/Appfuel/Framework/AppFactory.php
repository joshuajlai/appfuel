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

/**
 * Responsible for creating objects required by the framework for 
 * initializaion, bootstrapping, dispatching and outputting.
 */
class AppFactory
{
	/**
	 * Used to register spl autoloader for the framework
	 *
	 * @return	Env\Autoloader
	 */
	static public function createAutoloader()
	{
		return new Env\Autoloader();
	}

	/**
	 * Use to set display_error and error reporting
	 *
	 * @return	Env\PHPError
	 */
	static public function createPhpError()
	{
		return new Env\PHPError();
	}

	/**
	 * Used to change the php include_path
	 *
	 * @return Env\IncludePath
	 */
	static public function createIncludePath()
	{
		return new Env\IncludePath();
	}
	
	/**
	 * @return Env\State
	 */
	static public function createEnvState(array $data)
	{
		return new Env\State($data);
	}

	/**
	 * @return Env\Timezone
	 */
	static public function createTimezone()
	{
		return new Env\Timezone();
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
