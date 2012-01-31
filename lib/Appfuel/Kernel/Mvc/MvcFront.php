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
	Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Kernel\KernelOutput,
	Appfuel\Kernel\OutputInterface,
	Appfuel\Kernel\Mvc\Filter\FilterManager,
	Appfuel\Kernel\Mvc\Filter\FilterManagerInterface;

/**
 * The front controller is used build the intialize context, run the pre
 * intercepting filters, dispatch to the mv action, handle any errors,
 * run post filters and output the results.
 */
class MvcFront implements MvcFrontInterface
{	
	/**
	 * Used to create the action based on the route and dispatch the context
	 * into that action
	 * @var MvcActionDispatcher
	 */
	protected $dispatcher = null;

	/**
	 * Used to create context for dispatching
	 * @var ContextBuilderInterface
	 */
	protected $contextBuilder = null;

	/**
	 * Apply Intercept filter logic before mvc action is dispatched
	 * @var FilterChainInterface
	 */
	protected $preChain = null;

	/**
	 * Apply Intercept filter logic after mvc action is dispatched
	 * @var FilterChainInterface
	 */
	protected $postChain = null;

	/**
	 * Application strategy console|ajax|html
	 * @var string
	 */
	protected $strategy = null;

	/**
	 * @var	 mixed string | RequestUriInterface
	 */
	protected $uri = null;

	/**
	 * @var AppInputInterface
	 */
	protected $input = null;

	/**
	 * @var OutputInterface
	 */
	protected $output = null;

	/**
	 * @param	MvcActionFactoryInterface	$factory
	 * @return	AppContext
	 */
	public function __construct(MvcActionDispatcherInterface $dispatcher = null,
								OutputInterface $output = null,
								InterceptChainInterface $preChain = null,
								InterceptChainInterface $postChain = null)
	{
		if (null === $dispatcher) {
			$dispatcher = new MvcActionDispatcher();
		}
			
		if (null === $output) {
			$output = new KernelOutput();
		}
	
		$contextBuilder = $dispatcher->getContextBuilder();
		if (null === $preChain) {
			$preChain = new InterceptChain($contextBuilder);
		}

		if (null === $postChain) {
			$postChain = new InterceptChain($contextBuilder);
		}

		$this->dispatcher = $dispatcher;
		$this->output	  = $output;
		$this->preChain	  = $preChain;
		$this->postChain  = $postChain;
		$this->contextBuilder = $contextBuilder;
	}

	/**
	 * @return	MvcActionDispatcherInterface
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return	InterceptChainInterface
	 */
	public function getPreChain()
	{
		return $this->preChain;
	}

	/**
	 * @return	InterceptChainInterface
	 */
	public function getPostChain()
	{
		return $this->postChain;
	}

	/**
	 * @return	ContextBuilderInterface
	 */
	public function getContextBuilder()
	{
		return $this->contextBuilder;
	}

	/**
	 * @return	OutputInterface
	 */
	public function getOutputEngine()
	{
		return $this->output;
	}

	/**
	 * @param	string	$route
	 * @param	string|RequestUriInterface
	 * @return	int
	 */
	public function runConsoleUri($uri)
	{
		/* clear any configuration and start fresh */			
		$useUri = true;
		$context = $this->getContextBuilder()
						->clear()
						->setStrategy('console')
						->setUri($uri)
						->defineInputFromSuperGlobals($useUri)
						->build();   
							
		return $this->run($context);
	}

	/**
	 * This will dispatch a route with the console strategy and define its
	 * inputs from the super global which means $_SERVER['argv']
	 *
	 * @param	string	$route
	 * @return	int	
	 */
	public function runConsoleRoute($route)
	{
		$useUri  = false;
		$context = $this->getContextBuilder()
						->clear()
						->setStrategy('console')
						->setRoute($route)
						->defineInputFromSuperGlobals($useUri)
						->build();

		return $this->run($context);
	}

	/**
	 * Configure the dispatcher to use ajax strategy then run
	 * 
	 * @return	int
	 */
	public function runAjax()
	{
		$useUri  = true;
		$context = $this->getContextBuilder()
						->clear()
						->setStrategy('ajax')
						->useServerRequestUri()
						->defineInputFromSuperGlobals($useUri)
						->build();

		return $this->run($context);
	}

	/**
	 * Configure the dispatcher to use html strategy then run
	 * 
	 * @return	int
	 */
	public function runHtml()
	{
		$useUri  = true;
		$context = $this->getContextBuilder()
						->clear()
						->setStrategy('html-page')
						->useServerRequestUri()
						->defineInputFromSuperGlobals($useUri);

		return $this->run($context);
	}

	/**
	 *  
	 * @param	string	$strategy	console|ajax|htmlpage
	 * @return	int
	 */
	public function run(MvcContextInterface $context)
	{
		/*
		 * use the dispatch fluent interface to build the context for 
		 * dispatching
		 */
		$dispatcher = $this->getDispatcher();
		$routDetail = $context->getRouteDetail(); 
		$preChain	= $this->getPreFilterChain();

		/* 
		 * intercepting filters allow business logic access to the context
		 * before the mvc actions proccessing. in config you can specify a
		 * single filter or a list of filters.
		 */
		$filters = kernelRegistry::getParam('mvc-pre-filters', array());
		if (is_string($filters)) {
			$filters = array($filters);
		}
		else if (! is_array($filters)) {
			$filters = array();
		}

		/*
		 * Route details and add specific filters to a single route, allowing
		 * specialized business logic to be applied to a given route before
		 * the mvc action is processed
		 */
		if ($routeDetail->isPreFilters()) {
			$filters = array_merge($filters, $routeDetail->getPreFilters());
		}

		$preChain->loadFilters($filters);
		$context = $chain->applyFilters($context);

		/*
		 * Only dispatch a context if its exit code is within the range of 
		 * success. Note console and html, ajax and api all follow http status
		 * codes.
		 */
		$exitCode = $context->getExitCode();
		if ($exitCode >= 200 && $exitCode < 300) {
			/* 
			 * the mvc action can completely replace the context by returning 
			 * a new one otherwise the reference to the context that was passed 
			 * in is used for the post intercepting filters
			 */
			$dispatcher->dispatch($context);

			$postChain = $this->getPostFilterChain();
			/* 
			 * Allows business logic access to the context after the mvc action 
			 * is processed and before output
			 */
			$filters = kernelRegistry::getParam('mvc-post-filters', array());
			if (is_string($filters)) {
				$filters = array($filters);
			}
			else if (! is_array($filters)) {
				$filters = array();
			}

			/* 
			 * The context could have be switched by pre filters so re need
			 * grab the route detail again
			 */
			$routeDetail = $context->getRouteDetail();
			if($routeDetail->isPostFilters()) {
				$filters = array_merge($filters,$routeDetail->getPostFilters());
			}
			$context = $postChain->applyFilters($context);
		}	


		/* render context based on the output engine that was set, not the
		 * strategy used
		 */
		$output = $this->getOutputEngine();
		$output->render($context);
		
		/*
		 * we get the exit code again because if might have changed with the
		 * mvc action or post filters
		 */
		return $context->getExitCode();
	}
}
