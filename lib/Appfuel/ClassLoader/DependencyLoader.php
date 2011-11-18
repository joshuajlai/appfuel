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
namespace Appfuel\ClassLoader;

use RunTimeException,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * The dependency loader will use an unregistered autoloader to manually 
 * require the files
 */
class DependencyLoader implements DependencyLoaderInterface
{
	/**
	 * List of dependency objects to be loaded
	 * @var	array
	 */
	protected $dependencies = array();

	/**
	 * Autoloader that is not registered will do the actual loading
	 * @var AutoloaderInterface
	 */
	protected $loader = null;

	/**
	 * Error message used to indicate why loading failed
	 * @var string
	 */
	protected $error = null;

	/**
	 * @param	string	$rootPath
	 * @return	ClassDependency
	 */
	public function __construct(AutoLoaderInterface $loader = null)
	{
		if (null === $loader) {
			$loader = new StandardAutoLoader();
		}
		$this->setLoader($loader);
	}

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @return	string
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @param	string	
	 * @return	ClassDependency
	 */
	public function addDependency(ClassDependencyInterface $dependency)
	{
		$this->dependencies[] = $dependency;
		return $this;
	}

	/**
	 * @param	ClassDependencyInterface $dependency
	 * @return	null
	 */
	public function loadDependency(ClassDependencyInterface $dependency)
	{
		$loader = $this->getLoader();
		$loader->clearPaths();

		$loader->addPath($dependency->getRootPath());
		$classes  = $dependency->getNamespaces();
		foreach ($classes as $class) {
			if (class_exists($class,false) || interface_exists($class,false)) {
				continue;
			}

			if (! $loader->loadClass($class)) {
				throw new RunTimeException("coun not locate class -($class)");
			}
		}

		$files = $dependency->getFiles();
		foreach($files as $file) {
			if (! file_exists($file)) {
				throw new RunTimeException("could not locate file -($file)");
			}
			require $file;
		}
	}

	/**
	 * @return	null
	 */
	public function load()
	{
		$dependencies = $this->getDependencies();
		foreach($dependencies as $dependency) {
			$this->loadDependency($dependency);
		}
	}

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	null
	 */
	protected function setLoader(AutoLoaderInterface $loader)
	{
		$this->loader = $loader;
	}
}
