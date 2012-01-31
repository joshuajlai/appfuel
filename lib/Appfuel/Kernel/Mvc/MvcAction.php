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

/**
 * The mvc action is the controller in mvc. The front controller always 
 * dispatches a context to be processed by the mvc action based on a 
 * route (obtained via request uri, generally) that maps to that mvc action.
 * Every mvc action can also dispatch calls (process context) to any other
 * mvc action based on route (and context building), which always mvc actions
 * to be used rather than duplicated. 
 */
class MvcAction implements MvcActionInterface
{
	/**
	 * Used to make a call to other mvc actions
	 * @var MvcActionDispatcherInterface
	 */
	protected $dispatcher = null;

	/**
	 * Used to build a context the dispatcher needs to call another action
	 * @var MvcContextBuilderInterface
	 */
	protected $contextBuilder = null;

	/**
	 * The route key this action is mapped to
	 * @var string
	 */
	protected $route = null;

	/**
	 * @param	string	$route
	 * @return	MvcAction
	 */
	public function __construct($route, 
								MvcActionDispatcherInterface $dispatcher = null,
								MvcContextBuilder $contextBuilder = null)
	{
		$this->setRoute($route);

		if (null === $dispatcher) {
			$dispatcher = new MvcActionDispatcher();
		}
		$this->setDispatcher($disp);

		if (null === $contextBuilder) {
			$contextBuilder = new MvcContextBuilder();
		}
		$this->setContextBuilder($contextBuilder);
	}

	/**
	 * @return	string
	 */
	public function getRoute()
	{
		return $this->route;
	}
	
	/**
	 * @param	MvcActionDispatcher
	 * @return	null
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return 	MvcContextBuilder
	 */
	public function getContextBuilder()
	{
		return $this->contextBuilder;
	}

	/**
	 * Must be implemented by concrete class
	 *
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context)
	{
		throw new LogicException("must implement concrete process");
	}

	/**
	 * @param	string	$route
	 * @param	string	$strategy
	 * @return	null
	 */
	public function callWithNoInputs($route, $strategy)
	{

		$context = $this->getContextBuilder()
						->clear()
						->setRoute($route)
						->setStrategy($strategy)
						->noInputRequired()
						->buildContext();

		$this->getDispatcher()
			 ->dispatch($context);
		
		return $context;
	}

	/**
	 * Manually configure the dispatcher to call another mvc action. Note
	 * the route is part of the uri, which can be a RequestUri or a string.
	 * if the mvc action returns a context it will override the context passed
	 * in.
	 *
	 * @param	MvcContextInterface $context
	 * @return	MvcContextInterface
	 */
	public function call(MvcContextInterface $context)
	{
		$this->getDispatcher()
			 ->dispatch($context);

		return $context;
	}

	/**
	 * @param	string	$routeKey
	 * @param	MvcContextInterface $old
	 * @return	MvcContextInterface
	 */
	public function callUseContext($routeKey, MvcContextInterface $context)
	{
		$dispatcher = $this->getDispatcher();

		$tmp = $this->getContextBuilder()
					->clear()
					->setStrategy($context->getStrategy())
					->setRoute($routeKey)
					->setInput($context->getInput())
					->buildContext();

		$tmp->load($context->getAll());
		$dispatcher->dispatch($tmp);

		/* transfer all assignments made by mvc action */
		$context->load($tmp->getAll());
		$view = $tmp->getView();
		if (! empty($view)) {
			$context->setView($view);
		}

		return $context;
	}

	/**
	 * @param	MvcActionDispatcherInterface $dispatcher
	 * @return	null
	 */
	protected function setDispatcher(MvcActionDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param	string	$route
	 * @return	null
	 */
	protected function setRoute($route)
	{
		$this->route = $route;
	}
}
