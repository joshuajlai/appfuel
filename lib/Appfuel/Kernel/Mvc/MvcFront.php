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
	Appfuel\Output\OutputEngineInterface,
	Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\KernelOutput,
	Appfuel\Kernel\KernelRegistry,
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
	 *  
	 * @param	string	$strategy	app-console|app-ajax|app-htmlpage
	 * @return	int
	 */
	public function run($strategy)
	{
		$dispatcher = $this->getDispatcher();
		
		/*
		 * use the dispatch fluent interface to build the context for 
		 * dispatching
		 */
		$context = $dispatcher->setStrategy($strategy)
							  ->useServerRequestUri()
							  ->defineInputFromSuperGlobals()
							  ->buildContext();

		/* 
		 * intercepting filters allow business logic access to the context
		 * before the mvc actions proccessing occurs
		 */
		$filters = KernelRegistry::getParam('intercepting-filters', array());
		$filterManager = $this->getFilterManager();
		$filterManager->loadFilters($filters);
		$filterManager->applyPreFilters($context);

		/* 
		 * the mvc action can completely replace the context by returning a new
		 * one otherwise the reference to the context that was passed in 
		 * is used for the post intercepting filters
		 */
		$result = $dispatcher->runDispatch($context);
		if ($result instanceof ContextInterface) {
			$context = $result;
		}

		/*
		 * post intercepting filters allow business logic access to the context
		 * after the mvc action has processed it
		 */
		$filterManager->applyPostFilters($context);
		$view = $context->getView();
		
		/*
		 * The only strategy that does not use http is the console. Mvc actions
		 * can add http headers in an array with the key 'http-headers'. These
		 * headers are an array of strings
		 */
		$output = $this->createOutputEngine($strategy);
		if ('console' !== $strategy) {
			$headers = $context->get('http-headers', array());
			$httpResponse = new HttpResponse($view, $headers);
			if (! empty($headers) && is_array($headers)) {
				$httpReponse->loadHeaders($headers);	 
			}
			$output->renderResponse($httpResponse);
		}
		else {
			$output->render($view);
		}

		return $context->getExitCode();
	}

	/**
	 * @param	string	$strategy
	 * @return	MvcOutput
	 */
	protected function createOutputEngine($strategy)
	{
		if (empty($strategy) || ! is_string($strategy)) {
			$err = 'output strategy must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		return new MvcOutput($strategy);
	}

	/**
	 * @return	MvcActionDispatcherInterface
	 */
	protected function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return	FilterManagerInterface
	 */
	protected function getFilterManager()
	{
		return $this->filterManager;
	}
}
