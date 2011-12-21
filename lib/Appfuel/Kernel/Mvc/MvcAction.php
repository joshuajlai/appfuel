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
	 * The route key this action is mapped to
	 * @var string
	 */
	protected $route = null;

	/**
	 * @param	string	$route
	 * @return	MvcAction
	 */
	public function __construct($route, MvcActionDispatcherInterface $disp)
	{
		$this->setRoute($route);
		$this->setDispatcher($disp);
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
	 * @param	array	$codes
	 * @return	bool
	 */
	public function isContextAllowed(array $codes)
	{
		return false;
	}

	/**
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context)
	{}

	/**
	 * @param	string	$uri
	 * @param	string	$strategy	dispatch as console|ajax|html
	 * @return	AppContextInterface
	 */
	public function callUri($uri, $strategy)
	{
		$dispatcher = $this->getDispatcher();
						
		$context = $dispatcher->clear()
							  ->setUri($uri)
							  ->setStrategy($strategy)
							  ->useUriForInputSource()
							  ->buildContext();

		$dispatcher->dispatch($context);
		return $context;
	}

	/**
	 * @param	string	$route
	 * @param	string	$strategy
	 * @return	null
	 */
	public function callWithNoInputs($route, $strategy)
	{
		$dispatcher = $this->getDispatcher();

		$context = $dispatcher->clear()
					->setRoute($route)
					->setStrategy($strategy)
					->noInputRequired()
					->buildContext();

		$dispatcher->dispatch($context);
		return $context;
	}

	/**
	 * Manually configure the dispatcher to call another mvc action. Note
	 * the route is part of the uri, which can be a RequestUri or a string.
	 * if the mvc action returns a context it will override the context passed
	 * in.
	 *
	 * @param	mixed	string|RequestUri $uri
	 * @param	string	$strategy
	 * @param	string	$method
	 * @param	array	input
	 * @return	AppContextInterface
	 */
	public function call($uri, $method, array $in, $strategy, $useUri = true)
	{
		$dispatcher = $this->getDispatcher();

		$useUri = ($useUri === true) ? true : false;

		$context = $dispatcher->clear()
							  ->setStrategy($strategy)
							  ->setUri($uri)
							  ->defineInput($method, $in, $useUri)
							  ->buildContext();

		$dispatcher->dispatch($context);
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
