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
namespace Appfuel;

/**
 * The AppBuilder is used to encapsulate the logic need to build an App object,
 * the application object used to run a user request. Because This is the fist
 * file the calling code is likely to use it will not be governed by an 
 * interface. It will also hold the responsibility of initializing the system.
 */
class AppBuilder
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

	/**
	 * Used to override appfuel's env class
	 * @var string
	 */
	protected $envClass = NULL;

	/**
	 * Used to override appfuel's app factory
	 * @var string
	 */
	protected $appFactoryClass = NULL;

	/**
	 * Used to override appfuel's initializer
	 * @var string
	 */
	protected $initClass = NULL;



	/**
	 * @param	string	$path 
	 * @return	AppBuilder
	 */
	public function __construct($path)
	{
		$this->setBasePath($path);

		$this->loadDependencies($path);		
		if (! defined('AF_BASE_PATH')) {
			define('AF_BASE_PATH', $path);
		}

	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return	mixed	NULL|string
	 */
	public function getEnvClass()
	{
		return $this->envClass;
	}

	/**
	 * @param	string	$name
	 * @return	AppBuilder
	 */
	public function setEnvClass($name)
	{
		$errMsg = "Env class must be a string";
		$this->envClass = $this->validateString($name, $errMsg);
		$this->envClass = $name;
		return $this;		
	}

	/**
	 * @return	mixed	NULL|string
	 */
	public function getAppFactoryClass()
	{
		return $this->appFactoryClass;
	}

	/**
	 * @param	string	$name
	 * @return	AppBuilder
	 */
	public function setAppFactoryClass($name)
	{
		$errMsg = "App Factory class must be a string";
		$this->appFactoryClass = $this->validateString($name, $errMsg);
		return $this;		
	}

	/**
	 * @return	mixed	NULL|string
	 */
	public function getInitializerClass()
	{
		return $this->initClass;
	}

	/**
	 * @param	string	$name
	 * @return	AppBuilder
	 */
	public function setInitializerClass($name)
	{
		$errMsg = "Initializer class must be a string";
		$this->initClass = $this->validateString($name, $errMsg);
		return $this;		
	}

	/**
	 * @param	string	$string		string to be validated
	 * @param	string	$err		error message when not valid
	 * @return	string
	 */
	protected function validateString($string, $err)
	{
		if (empty($string) || ! is_string($string)) {
			throw new \Exception("Validation Error: $err");
		}
		
		return $string;
	}

	/**
	 * @param	string	$path
	 * @return	AppBuilder
	 */
	protected function setBasePath($path)
	{
		$errMsg = "Base path must be a string";
		$this->basePath = $this->validateString($path, $errMsg);
		return $this;
	}

	/**
	 * Resolves the path to the Dependecy class and loads app fuel dependent
	 * files. Note that these files are located at the lib directory off the
	 * base directory
	 *
	 * @param	string	$basePath
	 * @return	NULL
	 */
	protected function loadDependencies($basePath)
	{
		$path = $basePath . DIRECTORY_SEPARATOR . 'lib';
		$file = $path     . DIRECTORY_SEPARATOR . 
				'Appfuel' . DIRECTORY_SEPARATOR . 'Dependency.php';

		if (! file_exists($file)) {
			throw new \Exception("Dependency file could not be found ($file)");
		}

		require_once $file;

		$depend = new Dependency($path);
		$depend->load();
	}

}
