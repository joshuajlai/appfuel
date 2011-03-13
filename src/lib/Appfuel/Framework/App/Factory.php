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
namespace Appfuel\Framework\App;

use Appfuel\Framework\Autoload\Autoloader;

/**
 */
class Factory implements FactoryInterface
{
	/**
	 * Initializer is responsible for putting the system in a known state
	 * globally by setting the autoloader ini settings for errors and 
	 * include paths etc..
	 *
	 * @return	Initializer
	 */
    public function createInitializer($basePath)
	{
		$loader = $this->createAutoloader();
		$err    = $this->createPhpError();
		return new Initializer($basePath, $err, $loader);
	}

	/**
	 * @return	Appfuel\Framework\Autoload\Autoloader
	 */
	public function createAutoloader()
	{
		return new Autoloader();
	}

	/**
	 * @return	PHPError
	 */
	public function createPhpError()
	{
		return new PHPError();
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
