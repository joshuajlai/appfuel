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
	Appfuel\Output\OutputEngineInterface,
	Appfuel\View\ViewTemplateInterface,
	Appfuel\Kernel\KernelOutput,
	Appfuel\Kernel\KernelRegistry,
	Appfuel\Log\Logger,
	Appfuel\Log\LoggerInterface,
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
	 * Used to handler errors that occur from the dispatcher and mvc action
	 * @var LoggerInterface
	 */	
	protected $logger = null;

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
		$dipatcher = $this->getDispatcher();
		$context   = $dispatcher->setStrategy($strategy)
							    ->useServerRequestUri()
							    ->defineInputFromSuperGlobals()
							    ->buildContext();

		$filters = KernelRegistry::getParam('intercepting-filters', array());
		$filterManager = $this->getFilterManager();
		$filterManager->loadFilters($filters);
		$filterManager->applyPreFilters($context);

		$route  = $dispatcher->getRoute();
		$view   = $dipatcher->runDispatch($route, $strategy, $context);
		/* 
		 * the context does not have the view added to allow post filters
		 * access to the view that was manipulated by the mvc action controller
		 */
		$context->add('app-view', $view);
		$filterManager->applyPostFilters($context);
		$view = $context->get('app-view', $view);
		
		/*
		 * The view returned by the mvc action controller can be any string
		 * or object that implements a __toString interface
		 */
		if (is_string($view) || is_callable(array($view, '__toString'))) {
			$content =(string) $view;
		}
		else {
			$content = '';
		}
		$exitCode = $context->getExitCode();

		/*
		 * The only strategy that does not use http is the console. Mvc actions
		 * can add http headers in an array with the key 'http-headers'. These
		 * headers are an array of strings
		 */
		$output = $this->createOutputEngine($strategy);
		if ('app-console' !== $strategy) {
			$headers = $context->get('http-headers', array());
			$httpResponse = new HttpResponse($context, $headers);
			if (! empty($headers) && is_array($headers)) {
				$httpReponse->loadHeaders($headers);	 
			}
			$output->renderResponse($httpResponse);
		}
		else {
			$output->render($content);
		}

		return $exitCode;
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

		return new MvcOuput($strategy);
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
