<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use RunTimeException,
	InvalidArgumentException;

/**
 * Loads config data into the configuration registry. The data can be from a
 * php file that returns an array or a json file, the data can also be just 
 * an array.
 */
class ConfigLoader
{
	/**
	 * @var	FileReader
	 */
	protected $reader = null;

	/**
	 * @param	FileReaderInterface $reader
	 * @return	ConfigLoader
	 */
	public function __construct(FileReaderInterface $reader = null)
	{
		if (null === $reader) {
			$reader = new FileReader(new FileFinder('app/config'));
		}

		$this->reader = $reader;
	}

	/**
	 * @param	string	$path
	 * @param	string	$path
	 * @return	bool
	 */
	public function loadFile($path, $section = null)
	{
		if (! is_string($path) || empty($path)) {
			$err = "";
		}
	}

	/**
	 * @param	mixed	$data
	 * @param	string	$section	part of the config array to use
	 * @return	bool
	 */
	public function load($data, $section = null)
	{
		if (is_string($data)) {
			$data = $this->loadFile($data);
		}
		else if (! is_array($data)) {
			$err  = 'first param of load must be a string (path to the config ';
			$err .= 'file) or an array (actual config data) ';
			throw new DomainException($data);
		}

		if (null === $section) {
			ConfigRegistry::setAll($data);
			return true;
		}

		if (! is_string($section)) {
			$err = 'section key must be a string';
			throw new InvalidArgumentException($err);
		}

		if (! isset($data[$section])) {
			$err = "config section -($section) not found in config data";
			throw new DomainException($err);
		}
		$data = $data[$section];
	}

	/**
	 * Initialize the kernel registry with configuration parameters. You can
	 * specify a path to the config file or a array of config parameters. The
	 * config is keyed by section when no section is given the section key
	 * taken is 'main'. The section is then assigned into the KernelRegistry.
	 *
	 * @param	mixed	null|string|array	$file
	 * @param	string	section
	 * @return	null
	 */
	public function initConfig($file = null, $section = null)
	{
		if (null === $file || is_string($file)) {
			$data = $this->getData('config/config', $file);
		}
		else if (! empty($file) && is_array($file)) {
			$data = $file;
		}


		if (null === $section) {
			$section = 'main';
		}
		else if (empty($section) || ! is_string($section)) {
			$err = "configuration key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if (! isset($data[$section]) || !is_array($data[$section])) {
			$err = "Could not find config with key -($section)";
			throw new InvalidArgumentException($err);
		}
		$config = $data[$section];

		if (isset($data['common']) && is_array($data['common'])) {
			$common = $data['common'];
			foreach ($common as $key => $value) {
				if (! array_key_exists($key, $config)) {
					$config[$key] = $value;
					continue;
				}

				if (is_array($config[$key]) && is_array($value)) {
					$config[$key] = array_merge($value, $config[$key]);
				}

			}
		}

		$max  = count($config);
		KernelRegistry::setParams($config);
		self::$status['kernal:config'] = "config intialized with $max items";
		return $this;
	}

	/**
	 * Make sure the config file exists and that it returns an associative
	 * array of config data. If no file path given the appfuel path is used
	 *
	 * @return	array
	 */
	public function getData($type, $file = null)
	{
		$path = $this->getConfigPath();
		$err  = "loading $type data failure: ";

		if (null === $file) {
			$file = "$path/$type.php";
		}

		if (! file_exists($file)) {
			throw new RunTimeException("$type file not found at -($file)");
		}

		$data = require $file;
		if (! is_array($data) &&  $data === array_values($data)) {
			$err .= " $type file must return an associative array";
			throw new RunTimeException($err);
		}
	
		return $data;	
	}

	/**
	 * @return	string
	 */
	public function getConfigPath()
	{
		return $this->configPath;
	}

	/**
	 * @return	array
	 */
	public function getStatus()
	{
		return self::$status;
	}

	/**
	 * @return	DependencyLoaderInterface
	 */
	public function getDependencyLoader()
	{
		return $this->loader;
	}

	/**
	 * @param	DependencyLoaderInterface	$loader
	 * @return	KernelIntializer
	 */
	public function setDependencyLoader(DependencyLoaderInterface $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * @param	MvcActionDispatcherInterface $dispatch,
	 * @param	FilterManagerInterface $filterManager,
	 * @param	OutputInterface $output
	 * @return	MvcFront
	 */
	public function buildFront(MvcDispatcherInterface $dispatcher = null,
								InterceptChainInterface $preChain = null,
								InterceptChainInterface $postChain = null)
	{
		$preList = KernelRegistry::getParam('pre-filters', array());
		if (null === $preChain) {
			$preChain = new InterceptChain();
		}
		
		if (is_array($preList) && ! empty($preList)) {
			$preChain->loadFilters($preList);
		}

		$postList = KernelRegistry::getParam('post-filters', array());
		if (null === $postChain) {
			$postChain = new InterceptChain();
		}
		
		if (is_array($postList) && ! empty($postList)) {
			$postChain->loadFilters($postList);
		}

		if (null === $dispatcher) {
			$dispatcher = new MvcDispatcher();
		}

		return new MvcFront($dispatcher, $preChain, $postChain);
	}

	/**
	 * @return	MvcFactory
	 */
	public function createMvcFactory()
	{
		return new MvcFactory();
	}
	/**
	 * @param	string	$path	absolute path to config file
	 * @return	null
	 */
	protected function setConfigPath($path)
	{
		if (empty($path) || !is_string($path)) {
			throw new Exception("config path must be a non empty string");
		}

		$this->configPath = $path;
	}

	/**
	 * Load all interfaces and classes used in autoloading or dependency
	 * loading. Because the autoloader or dependency loaders are not 
	 * available we have to manually require each class
	 *
	 * @return	 null
	 */
	protected function initDependencyLoader()
	{
		/*
		 * obtuse name is allow the strings to be less than 80 chars
		 * p - path
		 * c - class name
		 */
		$p = AF_LIB_PATH . '/Appfuel/ClassLoader';
		$c = "\Appfuel\ClassLoader";
		$kpath  = AF_LIB_PATH . '/Appfuel/Kernel/KernelDependency.php';
		$kclass = "\Appfuel\Kernel\KernelDependency";
		$files  = array(
			"$c\AutoLoaderInterface"	   => "$p/AutoLoaderInterface.php",
			"$c\DependencyLoaderInterface" =>"$p/DependencyLoaderInterface.php",
			"$c\ClassDependencyInterface"  => "$p/ClassDependencyInterface.php",
			"$c\\NamespaceParserInterface" => "$p/NamespaceParserInterface.php",
			"$c\\NamespaceParser"		   => "$p/NamespaceParser.php",
			"$c\StandardAutoLoader"		   => "$p/StandardAutoLoader.php",
			"$c\ClassDependency"		   => "$p/ClassDependency.php",
			"$c\DependencyLoader"		   => "$p/DependencyLoader.php",
			$kclass						   => $kpath
		);

		$err = "dependent file not found";
		foreach ($files as $namespace => $file) {
			if (class_exists($namespace, false) || 
				interface_exists($namespace, false)) {
				continue;
			}
			if (! file_exists($file)) {
				throw new RunTimeException("$err -($file)");
			}
			require $file;
		}
	
		$this->setDependencyLoader(new DependencyLoader());
	}

	/**
	 * Load all dependent kernel php files so they do not have to be discoverd
	 * by the autoloader which is slower
	 *
	 * @return	null
	 */
	protected function initKernelDependencies()
	{
		$this->getDependencyLoader()
			 ->loadDependency(new KernelDependency());
		self::$status['kernel:dependency'] = "kernel files loaded";
	}
}
