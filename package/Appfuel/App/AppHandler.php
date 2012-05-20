<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use LogicException,
	DomainException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\ViewInterface,
	Appfuel\ClassLoader\ManualClassLoader,
	Appfuel\Config\ConfigRegistry,
	Appfuel\Kernel\TaskHandlerInterface,
	Appfuel\Kernel\Mvc\RequestUriInterface,
	Appfuel\Kernel\Mvc\AppInputInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
	Appfuel\Kernel\Mvc\MvcFactoryInterface;

/**
 * 
 */
class AppHandler implements AppHandlerInterface
{
	/**
	 * Holds the details of the top level directory structure as well as
	 * the location of config files and environments
	 * @var AppDetailInterface
	 */
	protected $detail = null;

	/**
	 * Used to run individual startup startegies
	 * @var TaskHandlerInterface
	 */
	protected $taskHandler = null;

	/**
	 * Used to create objects needed by the application infrastructure
	 * @var AppFactoryInterface
	 */
	protected $factory = null;

	/**
	 * Ensure base path and library paths are correct. Setup Contants.
	 * Load Kernel Dependencies
	 *
	 * @param	AppDetailInterface	
	 * @return	AppHandler
	 */
	public function __construct(AppDetailInterface $detail)
	{
		$this->detail = $detail;

	}

	/**
	 * @return	AppDetailInterface
	 */
	public function getAppDetail()
	{
		return $this->detail;
	}
	
	/**
	 * Create the app factory and task handler and define constants
	 * @param	array	$tasks 
	 * @return	null
	 */
	public function initialize(array $tasks = null)
	{
		$default = 'Appfuel\App\AppFactory';
		$class   = ConfigRegistry::get('app-factory-class', $default);
		if (! is_string($class) || empty($class)) {
			$err = "app factory class must be a non empty string";
			throw new DomainException($err);
		}

		if (! class_exists($class, false)) {
			$err  = "the app factory class should be added to the ";
			$err .= "kernel dependency file because it is needed before the ";
			$err .= "the autoloader is in use";
			throw new LogicException($err);
		}

		$factory = new $class();
		if (! $factory instanceof AppFactoryInterface) {
			$err  = "app factory -($class) must implment Appfuel\Kernel";
			$err .= "\AppFactoryInterface";
			throw new LogicException($err);
		}

		$handler = $factory->createTaskHandler();
		$this->factory = $factory;
		$this->setTaskHandler($handler);

		if (null !== $tasks) {
			$this->runTasks($tasks);
		}
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

	/**
	 * @param	array	$data
	 * @return	AppInputInterface
	 */
	public function createConsoleInput(array $data)
	{
		return $this->getAppFactory()
					->createConsoleInput($data);
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
	public function runTasks(array $tasks)
	{
		$this->getTaskHandler()
			 ->runTasks($tasks);

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

}
