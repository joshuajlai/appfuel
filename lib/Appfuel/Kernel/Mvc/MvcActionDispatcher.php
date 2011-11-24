<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Error\ErrorStackInterface,
	Appfuel\View\ViewTemplateInterface;

/**
 * Provide a fluent interface used to build the context required for 
 * for dispatching. Also resolves the route key to an action namespace 
 * with the KernelRegistry. This is used by the front controller to dispatch
 * the intial request and also used by mvc actions to call other actions.
 */
class MvcActionDispatcher implements MvcActionDispatcherInterface
{	
	/**
	 * Used to create mvc actions and views
	 * @var ActionFactoryInterface
	 */
	protected $factory = null;

	/**
	 * Used to generate the context needed to dispatch
	 * @var ContextBuilderInterface
	 */
	protected $builder = null;

	/**
	 * Uri object which holds the route and get params
	 * @var RequestUriInterface
	 */
	protected $uri = null;

	/**
	 * Route key used to determine the action namespace
	 * @var string
	 */
	protected $route = null;

	/**
	 * Strategy used to process the context. This will be translated into
	 * the method the dispatcher will call on the mvc action
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * Application input used in the context that will be processed
	 * @var	AppInputInterface
	 */
	protected $input = null;

	/**
	 * Acl role codes added to the context and used by the mvc action 
	 * @var array
	 */
	protected $aclCodes = array();
	
	/**
	 * This will override the error stack used in the application context
	 * @var ErrorStack
	 */
	protected $errorStack = null;

	/**
	 * @param	MvcActionFactoryInterface	$factory
	 * @return	AppContext
	 */
	public function __construct(MvcActionFactoryInterface $factory = null,
								ContextBuilderInterface $builder = null)
	{
		if (null === $factory) {
			$factory = new MvcActionFactory();
		}
		$this->factory = $factory;

		if (null === $builder) {
			$builder = new ContextBuilder();
		}
		$this->builder = $builder;
	}

	/**
	 * This will determine which mvc action method is used to process the 
	 * context
	 *
	 * @param	string	$strategy 
	 * @return	MvcActionDispatcher
	 */
	public function setStrategy($strategy)
	{
		if (empty($strategy) || ! is_string($strategy)) {
			$err = 'failed dispatch: strategy must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$strategy = strtolower($strategy);

		$valid = array('app-htmlpage', 'app-console', 'app-ajax');
		if (! in_array($strategy, $valid, true)) {
			$err  = 'failed dispatch: strategy must be on of the following ';
			$err .= '-(' . implode('|', $valid) . ')';
			throw new InvalidArgumentException($err);
		}

		$this->strategy = $strategy;
		return $this;
	}

	/**
	 * @param	string	$code
	 * @return	MvcActionDispatcher
	 */
	public function addAclCode($code)
	{
		if (empty($code) || ! is_string($code)) {
			$err = 'failed to dispatch: role code must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		/* no duplicates */
		if (in_array($code, $this->aclCodes)) {
			return $this;
		}

		$this->aclCodes[] = $code;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	MvcAtionDispatcher
	 */
	public function addAclCodes(array $list)
	{
		foreach ($list as $code) {
			$this->addAclCode($code);
		}

		return $this;
	}

	/**
	 * Manual set the RequestUri by passing in a string (context builder will 
	 * create it) or an object using the correct interface
	 *
	 * @param	RequestUriInterface $uri
	 * @return	MvcActionDispatcher
	 */
	public function setUri($uri)
	{
		if (is_string($uri)) {
			$builder = $this->getContextBuilder();
			$uri = $builder->createUri($uri);
		}
		else if (! ($uri instanceof RequestUriInterface)) {
			$err  = "failed dispatch: uri must be a string or an object that ";
			$err .= "implements Appfuel\Kernel\Mvc\\RequestUriInterface";
			throw new InvalidArgumentException($err);
		}

		$this->uri = $uri;
		return $this->setRoute($uri->getRouteKey());
	}

	/**
	 * Manually determine the route key to use in dispatching
	 *
	 * @param	string $route
	 * @return	MvcActionDispatcher
	 */
	public function setRoute($route)
	{
		if (! is_string($route)) {
			$err = 'dispatch failed: route key must be a string';
			throw new InvalidArgumentException($err);
		}
		
		$this->route = $route;
		return $this;
	}

	/**
	 * Generates an RequestUri using the super global $_SERVER['REQUEST_URI']
	 * as the uri string
	 *
	 * @return	MvcActionDispatcher
	 */
	public function useServerRequestUri()
	{
		$builder = $this->getContextBuilder();
		$uri = $builder->useServerRequestUri()
					   ->getUri();

		return $this->setUri($uri);
	}

	/**
	 * This will allow you to manual define the input used in the context 
	 * that will be dispatched. If a uri has also been defined then its 
	 * parameters will be used as the inputs get parameters by default. If
	 * you already have get parameters then the uri params will be merged
	 *
	 * @param	string	$method	 get|post or cli
	 * @param	array	$params	 input parameters
	 * @param	bool	$useUri  flag used to determine if the get parameters
	 *							 will be obtained from the uri
	 * @return	MvcActionDispatcher
	 */
	public function defineInput($method, array $params, $useUri = true)
	{
		if (true === $useUri) {
			$uri = $this->getUri();
			if (! ($uri instanceof RequestUriInterface)) {
				$err  = "defineInput failed: uri is required for its get ";
				$err .= "params, but has not been set";
				throw new RunTimeException($err);
			}
			$getParams = $uri->getParams();
			if (array_key_exists('get', $params)) {
				$getParams = array_merge($params['get'], $getParams);
			}
			$params['get'] = $getParams;
		}

		$builder = $this->getContextBuilder();
		$input   = $builder->createInput($method, $params);
		$this->input = $input;
		return $this;
	}

	/**
	 * This will create inputs from the php super globals $_POST, $_FILES,
	 * $_COOKIE and $_SERVER['argv']. If useUri is true the get params will
	 * be used from the uri otherwise if you $_GET you will have to manual
	 * define it your self
	 * 
	 * @return	MvcActionDispatcher
	 */
	public function defineInputFromSuperGlobals($useUri = true)
	{
		$builder = $this->getContextBuilder();
		if (true === $useUri) {
			$uri = $this->getUri();
			if (! ($uri instanceof RequestUriInterface)) {
                $uri = $this->useServerRequestUri()
                            ->getUri();	
			}
			$builder->setUri($uri);	
		}

		$this->input = $builder->buildInputFromDefaults($useUri)
							   ->getInput();

		return $this;
	}

	/**
	 * Will use the parameters from the uri object as the getParams for the
	 * input and set the input method to 'get'
	 *
	 * @return	MvcActionDispatcher
	 */
	public function useUriForInputSource()
	{
		return $this->defineInput('get', array(), true);
	}

	/**
	 * @return	MvcActionDispatcher
	 */
	public function noInputRequired()
	{
		return $this->defineInput('get', array(), false);	
	}
	
	/**
	 * @param	ErrorStackInterface $error
	 * @return	MvcActionDispatcher
	 */
	public function overrideContextErrorStack(ErrorStackInterface $error)
	{
		$this->errorStack = $error;
		return $this;
	}

	/**
	 * In order to dispatch we need the route, strategy and context. 
	 * The route and strategy are set using the interface. We need to 
	 * build the context using uri, and app input. Once we have a context
	 * we can than perform a runDispatch
	 *
	 * @return	AppContextInterface
	 */
	public function buildContext()
	{
		$err = 'Failed to dispatch: ';
		$builder  = $this->getContextBuilder();
		$uri      = $this->getUri();
		if (! ($uri instanceof RequestUriInterface)) {
			$uri = $builder->createUri('');
		}
		$builder->setUri($uri);

		$input = $this->getInput();
		if (! ($input instanceof AppInputInterface)) {
			$err .= 'input is required but not set';
			throw new RunTimeException($err);
		}
		$builder->setInput($input);

		$error = $this->getErrorStack();
		if ($error instanceof ErrorStackInterface) {
			$builder->setErrorStack($error);
		}

		$context = $builder->build();
		$codes = $this->getRoleCodes();
		foreach ($codes as $code) {
			$context->addAclRoleCode($code);
		}

		return $context;
	}

	/**
	 * Dispatch a request a context using the fluent interface
	 *
	 * @return	AppContextInterface
	 */
	public function dispatch()
	{
		$route = $this->getRoute();
		if (! is_string($route)) {
			$err = 'dispatch could not be started: route not set';
			throw new RunTimeException($err);
		}

		$strategy = $this->getStrategy();
		if (! is_string($strategy)) {
			$err = 'dispatch could not be started strategy not set';
			throw new RunTimeException($err);
		}
		return $this->runDispatch($route, $strategy, $this->buildContext());
	}

	/**
	 * Run the dispatch without any use of the fluent interface
	 *
	 * @param	string	$route
	 * @param	string	$strategy	
	 * @param	AppContextInterface $context
	 * @return	AppContextInterface
	 */
	public function runDispatch($route, $strategy, AppContextInterface $context)
	{
		$err = 'Failed to dispatch: ';
		if (! is_string($route)) {
			$err .= 'route key must be a string';
			throw new InvalidArgumentException($err);
		}

		$namespace = KernelRegistry::getActionNamespace($route);
		if (false === $namespace) {
			throw new RouteNotFoundException($route, '');
		}

		$factory = $this->getActionFactory();
		$action  = $factory->createMvcAction($namespace);
		
		/* Create a new dipatcher for the mvc action giving it the ability 
		 * to call other action controllers based on the route key 
		 */		
		$action->setDispatcher(new self($factory, $this->getContextBuilder()));

		/*
		 * Acl codes are simple way of giving the action controllers an easy
		 * way to restrict access. The role codes are completely controlled by
		 * the developer the dispatcher simply asks the question is this 
		 * context allowed to be processed based on these codes
		 */
		if (false === $action->isContextAllowed($context->getAclRoleCodes())) {
			throw new RouteDeniedException($route, '');		
		}

		/*
		 * Appfuel decided to seperate context type into 3 but, soon to be
		 * 4 interfaces. The htmlpage are strictly for page load in the web
		 * app. The ajax is for async calls by the web app. The console are
		 * for cli calls. Soon an api interface will added for the apps api
		 * calls. This helps create a seperation of concerns while allowing
		 * the mvc action to reuse business domains to achieve its goals.
		 */
		switch ($strategy) {
			case 'app-htmlpage':
				$view   = $factory->createHtmlView($namespace);
				$result = $action->processHtml($context, $view);	
				break;
			case 'app-ajax':
				$view   = $factory->createAjaxView($namespace);
				$result = $action->processAjax($context, $view);	
				break;
			case 'app-console':
				$view   = $factory->createConsoleView($namespace);
				$result = $action->processConsole($context, $view);
				break;
			default:
				$err  = "failed to dispatch: application type is not ";
				$err .= "recognized no interface exists for -($strategy)";
				throw new RunTimeException($err);
		}

		if (! ($result instanceof ViewTemplateInterface)) {
			$result = $view;
		}
		
		return $result;
	}

	/**
	 * @return	MvcActionFactoryInterface
	 */
	protected function getActionFactory()
	{
		return $this->factory;
	}

	/**
	 * @return	ContextBuilderInterface
	 */
	protected function getContextBuilder()
	{
		return $this->builder;
	}

	/**
	 * @return	string
	 */
	protected function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return	string
	 */
	protected function getStrategy()
	{
		return $this->strategy;
	}

	/**
	 * @return	RequestUriInterface
	 */
	protected function getUri()
	{
		return $this->uri;
	}

	/**
	 * @return	AppInputInterface
	 */
	protected function getInput()
	{
		return $this->input;
	}

	/**
	 * @return	array
	 */
	protected function getRoleCodes()
	{
		return $this->aclCodes;
	}

	/**
	 * @return	ErrorStackInterface
	 */
	protected function getErrorStack()
	{
		return $this->errorStack;
	}
}
