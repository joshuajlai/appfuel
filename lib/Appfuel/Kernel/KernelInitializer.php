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
namespace Appfuel\Kernel;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcFront,
	Appfuel\Kernel\Mvc\MvcRouteManager,
	Appfuel\Kernel\Mvc\InterceptChain,
	Appfuel\Kernel\Mvc\InterceptChainInterface,
	Appfuel\Kernel\Mvc\MvcDispatcher,
	Appfuel\Kernel\Mvc\MvcDispatcherInterface,
	Appfuel\Kernel\Mvc\MvcContextBuilder,
	Appfuel\Kernel\Startup\StartupTaskInterface,
	Appfuel\ClassLoader\DependencyLoader,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\DependencyLoaderInterface;

/**
 * The kernal intializer uses the kernal registry to get a list of start up
 * tasks. It will run through each task calling its execute methos. 
 */
class KernelInitializer 
{
	/**
	 * Absolute path to the config file
	 * @var string
	 */
	protected $configPath = null;

	/**
	 * Flag used during init to determine if a domain map will be used
	 * @var	bool
	 */
	protected $isDomainMap = true;

	/**
	 * Used to load any ClassDependecy objects
	 * @var DependecyLoaderInterface
	 */
	protected $loader = null;

	/**
	 * Each task has a status string which we save
	 * @var array
	 */
	static protected $status = array();

	/**
	 * When used over and over again the constant will be taken before
	 * the value passed in. There should only be one base path and app
	 * type.
	 *
	 * @return	KernalInitializer
	 */
	public function __construct($base, $lib = null)
	{
		$err = 'Initialization error: ';
		if (defined('AF_BASE_PATH')) {
			$base = AF_BASE_PATH;
		}
		else {
			define('AF_BASE_PATH', $base);
		}
			
		if (empty($base) || ! is_string($base)) {
			$err .= 'base path must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (null === $lib) {
			$lib = 'lib';
		}

		if (empty($lib) || ! is_string($lib)) {
			$err = 'the library directory must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (! defined('AF_LIB_PATH')) {
			define('AF_LIB_PATH', AF_BASE_PATH . "/$lib");
		}

		if (! file_exists(AF_LIB_PATH)) {
			$err = 'library path must exist before initialization';
			throw new RunTimeException($err);
		}

		$this->setConfigPath("$base/app");
		$this->initDependencyLoader();
		$this->initKernelDependencies();
	}

	/**
	 * A wrapper to perform for main intialization tasks.
	 * 1) users config settings into the KernelRegistry
	 * 2) default timezone 
	 * 3) php include path
	 * 4) load any application dependency objects
	 * 5) errors and fault handler
	 * 6) appfuel autoloader
	 * 7) load domain map is enabled
	 * 8) load route mapping
	 * 9) run any registered startup tasks. (tasks are held in the config)
	 *
	 * @param	null|string|array	$file
	 * @param	string				$configSection
	 * @param	null|string|array	$domain
	 * @param	null|string|array	$route
	 * @return	null
	 */
	public function initialize($section = 'main', 
							   $config = null, 
							   $domain = null,
							   $route = null)
	{
		$this->initConfig($config, $section)
			 ->initTimezone()
			 ->initIncludePath()
			 ->initFaultHandling()
			 ->initAppfuelAutoLoader()
			 ->initAppDependencies();

		$this->initRouteMap($route)
			 ->runStartupTasks();

		return $this;
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
				if (! isset($config[$key]) || ! is_array($config[$key])) {
					$config[$key] = $value;
				}
				else {
					$config[$key] = array_merge(
						$value,
						$config[$key]
					);
				}
			}
		}

		$max  = count($config);
		KernelRegistry::setParams($config);
		self::$status['kernal:config'] = "config intialized with $max items";
		return $this;
	}

	/**
	 * Set the php include path based on paths in the config. A path can
	 * be an actual string of include paths with the path seperator already
	 * taken care of or an array of paths for which we will concatenate and
	 * add the path
	 *
	 * @return	KernelIntializer
	 */
	public function initIncludePath()
	{
		$path   = KernelRegistry::getParam('include-path', array());
		$action = KernelRegistry::getParam('include-path-action', 'replace');
		if (empty($path)) {
			$msg = 'include path not initialized';
			self::$status['kernal:include-path'] = $msg;
			return $this;
		}
		
		$include = new IncludePath();
		$include->setPath($path, $action);
		$msg = 'include path initialized';
		self::$status['kernel:include-path'] = $msg;
		return $this;
	}

	/**
	 * The dependecy loader will load any ClassDependecyInterface added
	 * under the label 'app-dependency'. This allows for loading php files
	 * that would not work with the autoloader or files you want to preload
	 * for performence reasons
	 * 
	 * @return	null
	 */
	public function initAppDependencies()
	{
		$list = KernelRegistry::getParam('app-dependency', array());
		if (empty($list)) {
			$msg = 'no app dependencies loaded';
			self::$status['kernel:app-dependency'] = $msg;
			return $this;
		}

		if (is_string($list)) {
			$list = array($list); 
		}
		else if (! is_array($list)) {
			$msg = 'invalid config, no app dependencies loaded';
			self::$status['kernel:app-dependency'] = $msg;
			return $this;
		}
		
		$maxloaded = 0;
		$loader = $this->getDependencyLoader();
		
		foreach ($list as $item) {
			if (is_array($item)) {
				$class = array_shift($item);
				$rootpath = array_shift($item);
			}
			else if (is_string($item)) {
				$class = $item;
				$rootpath = null;
			}
			else {
				continue;
			}
			$loader->loadDependency(new $class($rootpath));
			$maxloaded++;
		}
		$msg = "loaded $maxloaded dependency objects";
		self::$status['kernel:app-dependency'] = $msg;
		return $this;
	}

	/**
	 * Initializing the fault handler will set the error_reporting level 
	 * enable or disable displaying errors and register error and exception
	 * handlers
	 *
	 * @return	KernelInitializer
	 */
	public function initFaultHandling()
	{
		$isErrorDisabled = KernelRegistry::getParam('disable-af-errors', false);
		$isFaultDisabled = KernelRegistry::getParam(
			'disable-af-fault-handler', 
			false
		);

		$isErrorDisabled = ($isErrorDisabled === true) ? true : false;
		$report = '';
		if (false === $isErrorDisabled) {
			$display = KernelRegistry::getParam('display-errors', 'off');
			if (null !== $display) {
				$errorDisplay = new Error\ErrorDisplay();
				$errorDisplay->set($display);
				$report .= 'display error ';
			}

			$level = KernelRegistry::getParam('error-level', 'all,strict');
			if (null !== $level) {
				$errorReporting = new Error\ErrorLevel();
				$errorReporting->setLevel($level);
				$report .= 'error reporting ';
			}
		}

		$isFaultDisabled = ($isFaultDisabled === true) ? true : false;
		if (false === $isFaultDisabled) {
			$handler = new FaultHandler();
			set_error_handler(array($handler, 'handleError'));
			set_exception_handler(array($handler, 'handleException'));
			$report .= 'fault handling';
		}
			
		self::$status['kernel:app-dependency'] = "initialized: $report";
		return $this;
	}

	/**
	 * @return	KernelInitializer
	 */
	public function initTimezone()
	{
		$defaultTz = 'America/Los_Angeles';
		$tz = KernelRegistry::getParam('default-timezone', $defaultTz);
		if ('disabled' === $tz) {
			return $this;
		}

		date_default_timezone_set($tz);
		$msg = "timezone initialized to $tz";
		self::$status['kernel:timezone'] = $msg;
		return $this;	
	}

	/**
	 * This will only register the appfuel standard autoloader. If you 
	 * what your own autoloader without appfuels then in the config
	 * use enable-af-autoloader = false
	 *
	 * @return	KernelInitializer
	 */
	public function initAppfuelAutoloader()
	{
		$isAutoloader = KernelRegistry::getParam('enable-af-autoloader', true);
		if (false == $isAutoloader) {
			return $this;
		}

		$loader = new StandardAutoLoader(AF_LIB_PATH);	
		$loader->register();

		$msg = "appfuel autoloader initialized";
		self::$status['kernel:autolaoder'] = $msg;
		return $this;	
	}

	/**
	 * A mapping of route keys to action controller namespaces is kept to 
	 * simplify the uri's generated and used by the framework. This is not
	 * optional and must used as part of the framework. In the uri the first
	 * component in the path or the name 'routekey' in the query string is
	 * the key for the route. When no key is found the route key '' is used.
	 * Here we will initialize the route list into the KernelRegistry
	 *
	 * @param	null|string|array	$routes
	 * @return	null
	 */
	public function initRouteMap($routes = null)
	{
		if (null === $routes || is_string($routes)) {
			$map = $this->getData('routes', $routes);
		}
		else if (! empty($routes) && is_array($routes)) {
			$map = $routes;
		}

		$max = count($map);
		MvcRouteManager::setRouteMap($map);
		$result = "route map intiailized with $max routes";
		self::$status['mvc route manager:routes'] = $result;
		return $this;
	}

    /**
	 * Startup tasks allow developers to write intialization strategies and
	 * register them in the config. We collection all the strategies and 
	 * execute them one at a time, storing the resulting status string in 
	 * out static collection of status results.
	 *  
	 * @return	null
     */
	public function runStartupTasks()
	{
		$err   = 'startup task init failure: '; 
		$tasks = KernelRegistry::getParam('startup-tasks', array());
		if (empty($tasks) || ! is_array($tasks)) {
			return $this;
		}

		foreach ($tasks as $taskClass) {
			$task = new $taskClass();
			if (! ($task instanceof StartupTaskInterface)) {
				$err .= "task -($taskClass) does not implement Appfuel\Kernal";
				$err .= "\StartupTaskInterface";
				throw new RunTimeException($err);
			}
			$keys = $task->getRegistryKeys();
			$params = array();
			foreach ($keys as $key => $default) {
				$value = KernelRegistry::getParam($key, $default);
				$params[$key] = $value;
			}
			$task->execute($params);
			$statusResult = $task->getStatus();
			if (empty($statusResult) || ! is_string($statusResult)) {
				$statusResult = 'status not reported';
			}

			self::$status[$taskClass] = $statusResult;
		}
	
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
