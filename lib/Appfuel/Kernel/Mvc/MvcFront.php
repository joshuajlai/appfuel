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
	 * Used to load and run the intercepting filters
	 * @var FilterManagerInterface
	 */
	protected $filterManager = null;

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
								FilterManagerInterface $filterManager = null,
								OutputInterface $output = null)
	{
		if (null === $dispatcher) {
			$dispatcher = new MvcActionDispatcher();
		}
		$this->dispatcher = $dispatcher;

		if (null === $filterManager) {
			$filterManager = new FilterManager();
		}
		$this->filterManager = $filterManager;

		if (null === $output) {
			$output = new KernelOutput();
		}
		$this->output = $output;
	}

	/**
	 * @return	MvcActionDispatcherInterface
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return	FilterManagerInterface
	 */
	public function getFilterManager()
	{
		return $this->filterManager;
	}

	/**
	 * @return	OutputInterface
	 */
	public function getOutputEngine()
	{
		return $this->output;
	}

	/**
	 * @param	string	$strategy	ajax|html|console
	 * @return	MvcFront
	 */
	public function setStrategy($strategy)
	{
		$this->getDispatcher()
			 ->setStrategy($strategy);
		return $this;
	}

	/**
	 * @param	string	$route
	 * @return	MvcFront
	 */
	public function setRoute($route)
	{
		$this->getDispatcher()
			 ->setRoute($route);

		return $this;
	}

	/**
	 * @param	array	$codes
	 * @return	MvcFront
	 */
	public function addAclCodes(array $codes)
	{
		$this->getDispatcher()
			 ->addAclCodes($codes);

		return $this;
	}

	/**
	 * @param	string	$code
	 * @return	MvcFront
	 */
	public function addAclCode($code)
	{
		$this->getDispatcher()
			 ->addAclCode($code);

		return $this;
	}

	/**
	 * @param	mixed	string|RequestUriInterface
	 * @return	MvcFront
	 */
	public function setUri($uri)
	{
		$this->getDispatcher()
			 ->setUri($uri);

		return $this;
	}

	/**
	 * @return	MvcFront
	 */
	public function useServerRequestUri()
	{
		$this->getDispatcher()
			 ->useServerRequestUri();

		return $this;
	}

    /**
     * @param   string  $method  get|post|cli
     * @param   array   $params
     * @param   bool    $useUri  use the uri for get parameters 
     * @return  MvcFront
     */
	public function defineInput($method, array $params, $useUri = true)
	{
		$this->getDispatcher()
			 ->defineInput($method, $params, $useUri);

		return $this;
	}

	/**
	 * @param	bool	$useUri 
	 * @return	MvcFront
	 */
	public function defineInputFromSuperGlobals($useUri = true)
	{
		$this->getDispatcher()
			 ->defineInputFromSuperGlobals($useUri);

		return $this;
	}

	/**
	 * @return	MvcFront
	 */
	public function useUriForInputSource()
	{
		$this->getDispatcher()
			 ->useUriForInputSource();

		return $this;
	}

	/**
	 * @return	MvcFront
	 */
	public function noInputRequired()
	{
		$this->getDispatcher()
			 ->noInputRequired();

		return $this;
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
		$dispatcher = $this->getDispatcher()
						   ->clear()
						   ->setStrategy('console')
						   ->setUri($uri)
						  ->defineInputFromSuperGlobals($useUri);
							
		return $this->run($dispatcher);
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
		$useUri = false;
		$dispatcher = $this->getDispatcher()
						   ->clear()
						   ->setStrategy('console')
						   ->setRoute($route)
						   ->defineInputFromSuperGlobals($useUri);

		return $this->run($dispatcher);
	}

	/**
	 * Configure the dispatcher to use ajax strategy then run
	 * 
	 * @return	int
	 */
	public function runAjax()
	{
		$useUri = true;
		$dispatcher = $this->getDispatcher()
						   ->clear()
						   ->setStrategy('ajax')
						   ->useServerRequestUri()
						   ->defineInputFromSuperGlobals($useUri);

		return $this->run($dispatcher);
	}

	/**
	 * Configure the dispatcher to use html strategy then run
	 * 
	 * @return	int
	 */
	public function runHtml()
	{
		$useUri = true;
		$dispatcher = $this->getDispatcher()
						   ->clear()
						   ->setStrategy('html')
						   ->useServerRequestUri()
						   ->defineInputFromSuperGlobals($useUri);

		return $this->run($dispatcher);
	}

	/**
	 *  
	 * @param	string	$strategy	console|ajax|htmlpage
	 * @return	int
	 */
	public function run(MvcActionDispatcherInterface $dispatcher)
	{
		/*
		 * use the dispatch fluent interface to build the context for 
		 * dispatching
		 */
		$context = $dispatcher->buildContext();

		/* 
		 * intercepting filters allow business logic access to the context
		 * before the mvc actions proccessing occurs
		 */
		$filters = KernelRegistry::getParam('intercepting-filters', array());
		$filterManager = $this->getFilterManager();
		$filterManager->loadFilters($filters);
		$result = $filterManager->applyPreFilters($context);
		
		/*
		 * Use the returned context in place of the one built
		 */
		if ($result instanceof MvcContextInterface) {
			$context = $result;
		}

		/*
		 * Only dispatch a context if its exit code is within the range of 
		 * success. Note console and html, ajax and api all follow http status
		 * codes.
		 */
		$exitCode = $context->getExitCode();
		if ($exitCode >= 200 || $exitCode < 300) {
			/* 
			 * the mvc action can completely replace the context by returning 
			 * a new one otherwise the reference to the context that was passed 
			 * in is used for the post intercepting filters
			 */
			$dispatcher->dispatch($context);

			/*
			 * post intercepting filters allow business logic access to the 
			 * context
			 * after the mvc action has processed it
			 */
			$filterManager->applyPostFilters($context);
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
