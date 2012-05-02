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

use LogicException,
	DomainException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\ViewInterface,
	Appfuel\ClassLoader\ManualClassLoader,
	Appfuel\Kernel\ConfigLoader,
	Appfuel\Kernel\ConfigRegistry,
	Appfuel\Kernel\TaskHandler,
	Appfuel\Kernel\TaskHandlerInterface,
	Appfuel\Kernel\Mvc\RequestUriInterface,
	Appfuel\Kernel\Mvc\AppInputInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
	Appfuel\Kernel\Mvc\MvcFactory,
	Appfuel\Kernel\Mvc\MvcFactoryInterface;

/**
 * 
 */
class AppHandler
{
	/**
	 * Relative path to file that holds a list of kernel dependencies
	 * @var string
	 */
	protected $dependFile = 'app/kernel-dependencies.php';

	/**
	 * List of tasks to run when framework initializes
	 * @var array
	 */
	protected $fwTasks = array(
		'Appfuel\Kernel\PHPIniTask',
		'Appfuel\Kernel\PHPErrorTask',
		'Appfuel\Kernel\PHPPathTask',
		'Appfuel\Kernel\PHPDefaultTimezoneTask',
		'Appfuel\Kernel\PHPAutoloaderTask',
		'Appfuel\Kernel\FaultHandlerTask',
		'Appfuel\Kernel\DependencyLoaderTask',
		'Appfuel\Kernel\RouteListTask',
	);

	/**
	 * @var TaskHandlerInterface
	 */
	protected $taskHandler = null;

	/**
	 * @var AppFactoryInterface
	 */
	protected $factory = null;

	/**
	 * Ensure base path and library paths are correct. Setup Contants.
	 * Load Kernel Dependencies
	 *
	 * @param	string	$base		absolute path to root dir of the app
	 * @param	array	$libDir		name of the directory php libraries live
	 * @return	HttpResponse
	 */
	public function __construct($base, $libDir = 'lib')
	{
        if (! defined('AF_BASE_PATH')) {
            define('AF_BASE_PATH', $base);
        }

		if (! $this->isValidDirectory(AF_BASE_PATH)) {
			$err  = 'base path must be a non empty string which is an ';
			$err .= 'absolute path to a directory -(' . AF_BASE_PATH . ') ';
			$err .= 'with no .. chars';
			throw new DomainException($err);
		}

		if (null === $libDir) {
			$libDir = 'lib';
		}

		if (! is_string($libDir)) {
			$err  = 'directory where the main php classes are kept must ';
			$err .= 'be a string';
			throw new InvalidArgumentException($err);
		}
	
		$libPath = $base . DIRECTORY_SEPARATOR . $libDir;
		if (! defined('AF_LIB_PATH')) {
			define('AF_LIB_PATH', $libPath);
		}

		if (! $this->isValidDirectory(AF_LIB_PATH)) {
			$err  = 'lib path must a valid directory under AF_BASE_PATH ';
			$err .= ' -(' . AF_LIB_PATH . ')';
			throw new DomainException($err);
		}
		$file = $this->getKernelDependencyPath();
		$this->loadKernelDependencies($file);

		$this->setAppFactory($this->createAppFactory());
	}
	
	/**
	 * @param	string	$file
 	 * @param	string	$section
	 * @param	bool	$isReplace
	 * @return	AppRunner
	 */
	public function loadConfigFile($file, $section = null, $isReplace = true)
	{
		$loader = $this->createConfigLoader();
		$loader->loadFile($file, $section, $isReplace);

		return $this;
	}

	/**
	 * @return	AppFactoryInterface
	 */
	public function getAppFactory()
	{
		return $this->factory;
	}

	/**
	 * @param	MvcFactoryInterface $factory
	 * @return	AppRunner
	 */
	public function setAppFactory(AppFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 * @return	MvcFactoryInterface
	 */
	public function createAppFactory()
	{
		return new AppFactory();
	}

	/**
	 * @return	RequestUriInterface
	 */
	public function createUriFromServerSuperGlobal()
	{
		return $this->getAppFactory()
					->createUriFromServerSuperGlobal();
	}

	/**
	 * @param	string
	 * @return	RequestUriInterface
	 */
	public function createUri($str)
	{
		return $this->getAppFactory()
					->createUri($str);
	}

	/**
	 * @return	AppInputInterface
	 */
	public function createRestInputFromBrowser($uri = null)
	{
		return $this->getAppFactory()
					->createRestInputFromBrowser($uri);
	}

	public function createRestInputFromConsole($uri = null)
	{
		return $this->getAppFactory()
					->createRestInputFromConsole($uri);
	}

	/**
	 * @param	array	$tasks
	 * @return	AppRunner
	 */
	public function findRoute($key, $format = null)
	{
		$factory = $this->getAppFactory();

		if ($key instanceof RequestUriInterface) {
			$format = $key->getRouteFormat();
			$key    = $key->getRouteKey();
		}
		else if (! is_string($key)) {
			$err  = 'first param must be a string or an object that ';
			$err .= 'implments Appfuel\Kernel\Mvc\RequestUriInterface';
			throw new DomainException($err);
		}

		$route = $factory->createRouteDetail($key);
		if (! empty($format)) {
			$route->setFormat($format);
		}

		return $route;
	}

	/**
	 * @param	string $key
	 * @param	AppInputInterface   $input
	 * @return	MvcContextInterface
	 */
	public function createContext($key, AppInputInterface $input)
	{
		return $this->getAppFactory()
					->createContext($key, $input);
	}

	/**
	 * @param	MvcRouteDetailInterface	$route
	 * @param	MvcContextInterface		$context
	 * @return	AppRunner
	 */
	public function initializeApp(MvcRouteDetailInterface $route, 
								  MvcContextInterface $context)
	{
		$handler = $this->loadTaskHandler();
		$handler->kernelRunTasks($route, $context);
		return $this;
	}

	/**
	 * @param	MvcRouteDetailInterface	$route
	 * @param	MvcContextInterface		$context
	 * @param	string					$format
	 * @return	AppRunner
	 */
	public function setupView(MvcRouteDetailInterface $route, 
							  MvcContextInterface $context, 
							  $format = null)
	{

		if (empty($format)) {
			$format = $route->getFormat();
		}

		$this->getAppFactory()
			 ->createViewBuilder()
			 ->setupView($context, $route, $format);

		return $this;
	}

	public function composeView(MvcRouteDetailInterface $route,
								MvcContextInterface $context)
	{
        if ($route->isViewDisabled()) {
            return '';
        }

        $view = $context->getView();
        if (is_string($view)) {
            $result = $view;
        }
        else if ($view instanceof ViewInterface) {
            $result = $view->build();
        }
        else if (is_callable(array($view, '__toString'))) {
            $result =(string) $view;
        }
        else {
            $err  = "view must be a string or an object the implements ";
            $err .= "Appfuel\View\ViewInterface or an object thtat implemnts ";
            $err .= "__toString";
            throw new DomainException($err);
        }
	
		return $result;
	}

	/**
	 * @param	MvcRouteDetailInterface $route
	 * @param	MvcContextInterface $context
	 * @param	bool	$isHttp
	 * @return	null
	 */
	public function outputHttpContext(MvcRouteDetailInterface $route, 
									  MvcContextInterface $context,
									  $version = '1.1')
	{
		$content = $this->composeView($route, $context);
		$status  = $context->getExitCode();
		$headers = $context->get('http-headers', null); 
		if (! is_array($headers) || empty($headers)) {
				$headers = null;
		}
		$factory = $this->getAppFactory();
		$response = $factory->createHttpResponse(
			$content, 
			$status, 
			$version, 
			$headers
		);

		$output = $factory->createHttpOutput();
		$output->render($response);
	}

	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function outputConsoleContext(MvcRouteDetailInterface $route,
										 MvcContextInterface $context)
	{
		$content = $this->composeView($route, $context);
		$output  = $this->getAppFactory()
						->createConsoleOutput();
		
		$output->render($content);
	}

	/**
	 * @param	MvcContextInterface		$context
	 * @return	AppRunner
	 */
	public function runAction(MvcContextInterface $context)
	{
		$context = $this->getAppFactory()
			            ->createFront()
			            ->run($context);

		return $context;
	}


	/**
	 * @param	array	$tasks
	 * @return	AppRunner
	 */
	public function initializeFramework(array $tasks = null)
	{
		if (null !== $tasks) {
			$this->loadFrameworkTasks($tasks);
		}

		$handler = $this->loadTaskHandler();
		$tasks   = $this->getFrameworkTasks();
		if (! empty($tasks)) {
			$handler->runTasks($tasks);
		}

		return $this;
	}


	/**
	 * @return	array
	 */
	public function getFrameworkTasks()
	{
		return $this->fwTasks;
	}

	/**
	 * @param	string	$name
	 * @return	AppRunner
	 */
	public function addFrameworkTask($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'task name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (in_array($name, $this->fwTasks, true)) {
			return $this;
		}

		$this->fwTasksp[] = $name;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	AppRunner
	 */
	public function loadFrameworkTasks(array $list)
	{
		foreach ($list as $name) {
			$this->addFrameworkTask($name);
		}

		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	AppRunner
	 */
	public function setFrameworkTasks(array $list)
	{
		$this->clearFrameworkTasks();
		$this->loadFrameworkTasks($list);
		return $this;
	}

	/**
	 * @return	AppRunner
	 */
	public function clearFrameworkTasks()
	{
		$this->fwTasks = array();
		return $this;
	}

	/**
	 * @return	TaskHandlerInterface
	 */
	public function getTaskHandler()
	{
		return $this->taskHandler;
	}

	/**
	 * @param	TaskHandlerInterface $handler
	 * @return	AppRunner
	 */
	public function setTaskHandler(TaskHandlerInterface $handler)
	{
		$this->taskHandler = $handler;
		return $this;	
	}

	/**
	 * @return	TaskHandlerInterface
	 */
	public function loadTaskHandler()
	{
		$handler = $this->getTaskHandler();
		if (! $handler instanceof TaskHandlerInterface) {
			$handler = $this->createTaskHandler();
			$this->setTaskHandler($handler);
		}

		return $handler;
	}

	/**
	 * @return	TaskHandlerInterface
	 */
	public function createTaskHandler()
	{
		return	$this->getAppFactory()
					 ->createTaskHandler();
	}

	/**
	 * Determines if a directory exists as the path given. Also ensures the
	 * path is absolute and no .. meta characters are used to change the path
	 * 
	 * @param	string	$path
	 * @return	bool
	 */
	protected function isValidDirectory($path)
	{
		if (! is_string($path) || 
			empty($path) ||
			DIRECTORY_SEPARATOR !== $path{0} ||
			false !== strpos($path, '../') ||
			! is_dir($path)) {
			return false;
		}

		return true;
	}

	/**
	 * Absolute path to a php file that returns an associative array of 
	 * class name => class path.
	 *
	 * @return 	string
	 */
	protected function getKernelDependencyPath()
	{
		return AF_BASE_PATH . DIRECTORY_SEPARATOR . $this->dependFile;
	}

	/**
	 * Ensure files the kernel depends on are loaded into memory before they
	 * are needed.
	 *
	 * @param	string	$file
	 * @return	null
	 */
	protected function loadKernelDependencies($file)
	{
		if (! file_exists($file)) {
			$err = "could not find kernel dependency file at -($file)";
			throw new DomainException($err);
		}

		if (! defined('AF_LIB_PATH')) {
			$err  = 'can not load kernel dependencies when AF_LIB_PATH is ';
			$err .= 'not defined';
			throw new LogicException($err);
		}

		$loaderClass = 'Appfuel\ClassLoader\ManualClassLoader';
		if (! class_exists($loaderClass, false)) {
			$loaderFile = AF_LIB_PATH   . DIRECTORY_SEPARATOR . 
						  'Appfuel'     . DIRECTORY_SEPARATOR .
						  'ClassLoader' . DIRECTORY_SEPARATOR .
						  'ManualClassLoader.php';
			if (! file_exists($loaderFile)) {
				$err  = "manual class loader is require to load kernel ";
				$err .= "dependencies but was not found at -($loaderFile)";
				throw new DomainException($err);
			}
			require $loaderFile;
		}

		$useLibPath = true;
		ManualClassLoader::loadCollectionFromFile($file, $useLibPath);
	}

	/**
	 * @return	ConfigLoader
	 */
	protected function createConfigLoader()
	{
		return new ConfigLoader();
	}
}
