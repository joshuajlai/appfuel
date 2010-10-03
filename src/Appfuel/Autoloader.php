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
namespace 	Appfuel;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class Autoloader
{
	/**
	 * Namespace Separator
	 * Used to resolve the incoming class name
	 * @var string
	 */
	protected $nsSeparator = NULL;
	
	/**
	 * File Extension
	 * @var string
	 */
	protected $fileExt = NULL;

	/**
	 * Path
	 * These are the paths used in the include path at the time
	 * of registration
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Constructor
	 * Assign the namespace separator for PHP 5.3 
	 *
	 * @return 	Autoloader
	 */
	public function __construct()
	{
		$this->setNamespaceSeparator('\\');
		$this->setFileExtension('.php');
	}

	/**
	 * @return 	string
	 */
	public function getNamespaceSeparator()
	{
		return $this->nsSeparator;
	}

	/**
	 * @param 	string 	$chars 	characters making up the namespace separator
	 * @return 	Autoloader
	 */
	public function setNamespaceSeparator($chars)
	{
		if (! is_string($chars)) {
			throw new \Exception(
				"Namespace separator must by a string"
			);
		}

		$this->nsSeparator = $chars;
		return $this;
	}

	/**
	 * @return 	string
	 */
	public function getFileExtension()
	{
		return $this->fileExt;
	}

	/**
	 * @param 	string 	$chars 	characters making up the file extension
	 * @return 	Autoloader
	 */
	public function setFileExtension($chars)
	{
		if (! is_string($chars)) {
			throw new \Exception(
				"Namespace separator must by a string"
			);
		}

		$this->fileExt = $chars;
		return $this;
	}

	/**
	 * Assign Include Paths
	 * This saves the include path to be used to determine if files exist
	 * or not
	 *
	 * @param 	array 	$paths
	 * @return 	Autolaoder
	 */
	public function assignIncludePath(array $paths)
	{
		$this->paths = $paths;
		return $this;
	}

	/**
	 * @return 	array
	 */
	public function getIncludePath()
	{
		return $this->paths;
	}

	/**
	 * Resolve Class Path
	 * Will convert PHP 5.3+ namespace separators to directory 
	 * separators
	 *
	 * @param 	string 	$className 	
	 * @return 	string
	 */
	public function resolveClassPath($className)
	{
		$ns = $this->getNamespaceSeparator();
		return str_replace($ns, DIRECTORY_SEPARATOR, $className);
	}

	/**
	 * Register 
	 * Wrapper for the spl_autoload_register. 
	 *
	 * @return 	bool
	 */
	public function register()
	{
		return spl_autoload_register(array($this, 'loadClass'));
	}

	/**
	 * Unregister 
	 * Wrapper for the spl_autoload_unregister
	 *
	 * @return 	bool
	 */
	public function unregister()
	{
		return spl_autoload_unregister(array($this, 'loadClass'));
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
		if ($this->isLoaded()) {
			return;
		}

		$ext  = $this->getExtension();
		$file = $this->resolveClassPath($className) . $ext;
		if (file_exists($file)) {
			require $file;
			return;
		}

		$paths = explode(':', get_include_path());
		foreach ($paths as $path) {
			$filePath = $path . DIRECTORY_SEPARATOR . $file;
			if (file_exists($filePath)) {
				require $filePath;
				return;
			}
		}

		throw new \Exception(
			"Autoload Error: could not file for class $className for file
			$file"
		);
	}
}

