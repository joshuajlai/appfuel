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
	Appfuel\Http\HttpResponse,
	Appfuel\Http\HttpOutput,
	Appfuel\Http\HttpOutputInterface,
	Appfuel\Console\ConsoleOutput,
	Appfuel\Console\ConsoleOutputInterface,
	Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\KernelRegistry,
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
								FilterManagerInterface $filterManager = null)
	{
		if (null === $dispatcher) {
			$dispatcher = new MvcActionDispatcher();
		}
		$this->dispatcher = $dispatcher;

		if (null === $filterManager) {
			$filterManager = new FilterManager();
		}
		$this->filterManager = $filterManager;
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
	 * @param	OutputInterface $output
	 * @return	MvcFront
	 */
	public function setOutputEngine(OutputInterface $output)
	{
		$strategy = $this->getDispatcher()
						 ->getStrategy();

		if (null === $strategy) {
			$err = 'strategy must be set before output engine is set';
			throw new RunTimeException($err);
		}

		if ('console' === $strategy) {
			if ($output instanceof ConsoleOutputInterface) {
				$this->output = $output;
				return $this;
			}
			else {
				$err  = 'for console strategy output engine must implement ';
				$err .= 'Appfuel\Console\ConsoleOutputInterface';
				throw new RunTimeException($err);
			} 
		}

		if (! ($output instanceof HttpOutputInterface)) {
			$err  = 'for html or ajax strategies output engine must implement ';
			$err .= 'Appfuel\Http\HttpOutputInterface';
			throw new RunTimeException($err);
		}

		$this->output = $output;
		return $this;
	}

	/**
	 * @return	OutputInterface
	 */
	public function getOutputEngine()
	{
		return $this->output;
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
	public function runConsoleUri($uri, ConsoleOutputInterface $out = null)
	{
		if (null === $out) {
			$out = $this->createConsoleOutput();
		}

		/* clear any configuration and start fresh */			
		$dispatcher = $this->getDispatcher()
						   ->clear();

		$this->setStrategy('console')
			 ->setOutputEngine($out);

		$useUri = true;
		$dispatcher->setUri($uri)
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
	public function runConsoleRoute($route, ConsoleOutputInterface $out = null)
	{
		if (null === $out) {
			$out = $this->createConsoleOutput();
		}


		$useUri = false;
		$dispatcher = $this->getDispatcher()
						   ->clear()
						   ->setStrategy('console')
						   ->setRoute($route)
						   ->setOutputEngine($out)
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
		$filterManager->applyPreFilters($context);
		
		/*
		 * Only dispatch a context if its exit code is within the range of 
		 * success. Note console and html, ajax and api all follow http status
		 * codes.
		 */
		$exitCode = $context->getExitCode();
		if ($exitCode >= 200 || $exitCode < 300) {
			/* 
			 * the mvc action can completely replace the context by returning 
			 * a ne one otherwise the reference to the context that was passed 
			 * in is used for the post intercepting filters
			 */
			$result = $dispatcher->runDispatch($context);
			if ($result instanceof AppContextInterface) {
				$context = $result;
			}

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
		$this->render($context);

		/*
		 * we get the exit code again because if might have changed with the
		 * mvc action or post filters
		 */
		return $context->getExitCode();
	}

	/**
	 * Decides which render interface to run based on the implementation of
	 * the output engine
	 *
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function render(AppContextInterface $context)
	{
		$output = $this->getOutputEngine();
		if ($output instanceof HttpOutputInterface) {
			$this->renderHttp($output, $context);
		}
		else if ($output instanceof ConsoleOutputInterface) {
			$this->renderConsole($output, $context);
		}
		else {
			$err  = 'render failed: output engine was not set or did not ';
			$err .= 'implement Appfuel\Http\HttpOutputInterface or ';
			$err .= 'Appfuel\Console\ConsoleOutputInterface';
			throw new RunTimeException($err);
		}
	}

	/**
	 * @param	HttpOutputInterface $output
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function renderHttp(HttpOutputInterface $output,
							  AppContextInterface $context)
	{
		$httpResponse = $context->get('http-response', null);
		if (! ($httpResponse instanceof HttpResponseInterface)) {
			$headers = $context->get('http-headers', array());
			$view    = $context->getView();
			$code	 = $context->getExitCode();
			$httpResponse = new HttpResponse($context->getView(), $code);
			if (! empty($headers) && is_array($headers)) {
				$httpReponse->loadHeaders($headers);	 
			}
		}

		$output->renderResponse($httpResponse);
	}

	/**
	 * @param	ConsoleOutputInterface $output
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function renderConsole(ConsoleOutputInterface $output,
								  AppContextInterface $context)
	{
		$output->render($context->getView());
	}

	/**
	 * @return	ConsoleOutput
	 */
	public function createConsoleOutputEngine()
	{
		return new ConsoleOutput();
	}

	/**
	 * @return	HttpOutput
	 */
	public function createHttpOutputEngine()
	{
		return new HttpOutput();
	}
}
