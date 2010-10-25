<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Autoloader;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
interface LoaderInterface
{
	/**
	 * @return 	string
	 */
	public function getRegisteredMethod();

	/**
	 * @param 	string 	$chars 	characters making up the file extension
	 * @return 	Autoloader
	 */
	public function setRegisteredMethod($chars);

	/**
	 * Register 
	 * Wrapper for the spl_autoload_register. 
	 *
	 * @return 	bool
	 */
	public function register($throw = TRUE, $prepend = FALSE);

	/**
	 * Unregister 
	 * Wrapper for the spl_autoload_unregister
	 *
	 * @return 	bool
	 */
	public function unregister();

	/**
	 * @return bool
	 */
	public function isLoaded($className);

	/**
	 * Load Class
	 * Will be registered to handle autoload requested class names
	 *
	 * @param 	string 	$className	
	 * @return 	void
	 */
	public function loadClass($className);

}

