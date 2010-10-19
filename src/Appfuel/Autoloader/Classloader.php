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
namespace 	Appfuel\Autoloader;

use Appfuel\Filesystem\Manager 	as FileManager;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class Classloader implements LoaderInterface
{
	/**
	 * Registered Method
	 * Name of the method to register
	 * @var string
	 */
	protected $registeredMethod = NULL;

	/**
	 * Constructor
	 * Assign the namespace separator for PHP 5.3 
	 *
	 * @return 	Autoloader
	 */
	public function __construct()
	{
		$this->setRegisteredMethod('loadClass');
	}

	/**
	 * @return 	string
	 */
	public function getRegisteredMethod()
	{
		return $this->registeredMethod;
	}

	/**
	 * @param 	string 	$chars 	characters making up the file extension
	 * @return 	Autoloader
	 */
	public function setRegisteredMethod($chars)
	{
		if (! is_string($chars)) {
			throw new \Exception(
				"Namespace separator must by a string"
			);
		}

		$this->registeredMethod = $chars;
		return $this;
	}

	/**
	 * Register 
	 * Wrapper for the spl_autoload_register. 
	 *
	 * @return 	bool
	 */
	public function register($throw = TRUE, $prepend = FALSE)
	{
		$methodName = $this->getRegisteredMethod();
		$params = array($this, $methodName);
		return spl_autoload_register($params, $thow, $prepend);
	}

	/**
	 * Unregister 
	 * Wrapper for the spl_autoload_unregister
	 *
	 * @return 	bool
	 */
	public function unregister()
	{
		$methodName = $this->getRegisteredMethod();
		return spl_autoload_unregister(array($this, $methodName));
	}

	/**
	 * @return bool
	 */
	public function isLoaded($className)
	{
		return class_exists($className, FALSE) || interface_exists($className);
	}

	/**
	 * Load Class
	 * Will be registered to handle autoload requested class names
	 *
	 * @param 	string 	$className	
	 * @return 	void
	 */
	public function loadClass($className)
	{
		if ($this->isLoaded($className)) {
			return;
		}

		$fileName = FileManager::classNameToFileName($className);
		$filePath = FileManager::getAbsolutePath($fileName);

		if (FALSE === $filePath) {
			throw new \Exception(
				"Autoload Error: could not find class: $className for file
				$fileName"
			);
		}

		require_once $filePath;
	}
}

