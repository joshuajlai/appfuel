<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     Util
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\StdLib\App;

use Appfuel\StdLib\Autoloader\LoaderInterface	as LoaderInterface;
use Appfuel\StdLib\Autoloader\Classloader		as Classloader;

/**
 * App Manager
 
 */
class Manager
{

	/**
	 * Autoloader
	 * @var 	\Appfuel\Autoloader\LoaderInterface
	 */
	static protected $autoloader = NULL;

	/**
	 * @param 	\Appfuel\Autoloader\LoaderInterface
	 * @return 	void
	 */	
	static public function setAutoloader(LoaderInterface $loader)
	{
		self::$autoloader = $loader;
	}

	/**
	 * @return \Appfuel\Autoloader\LoaderInterface
	 */
	static public function getAutoloader()
	{
		return self::$autoloader;
	}

	/**
	 * @return bool
	 */
	static public function isAutoloader()
	{
		return self::getAutoloader() instanceof LoaderInterface;
	}

	/**
	 * @return void
	 */
	static public function clearAutoloader()
	{
		self::$autoloader = NULL;
	}

	/**
	 * When no autoloader is available we will create a 
	 * Appfuel\Autoloader\Classloader and register it otherwise we will
	 * get whatever autoloader is set and register that
	 *
	 * @return void
	 */
	static public function enableAutoloader()
	{
		if (! self::isAutoloader()) {
			self::setAutoloader(new ClassLoader());
		}

		$autoloader = self::getAutoloader();
		$autoloader->register();
	}

	/**
	 * Unregister the current autoloader
	 *
	 * @return void
	 */
	static public function disableAutoloader()
	{
		if (! self::isAutoloader()) {
			return;
		}

		$loader = self::getAutoloader();
		$loader->unregister();
	}
}
