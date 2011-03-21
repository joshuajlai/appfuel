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
 */
class AppFactory implements AppFactoryInterface
{
	/**
	 * @return	Autoloader
	 */
	public function createAutoloader()
	{
		return new Init\Autoloader();
	}

	/**
	 * @return	PHPError
	 */
	public function createPhpError()
	{
		return new Init\PHPError();
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
