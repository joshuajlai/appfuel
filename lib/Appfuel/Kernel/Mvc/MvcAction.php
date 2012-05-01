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

use Appfuel\Orm\OrmManager;

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
	 * @var MvcFactoryInterface
	 */
	protected $factory = null;

	/**
	 * @param	MvcFactoryInterface	$factory
	 * @return	MvcAction
	 */
	public function __construct(MvcFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = new MvcFactory();
		}
		$this->setMvcFactory($factory);
		$this->setDispatcher($factory->createDispatcher());
	}

	/**
	 * @param	string	$key
	 * @return	OrmRepositoryInterface
	 */
	public function getRepository($key, $source = 'db')
	{
		return OrmManager::getRepository($key, $source);
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
	public function getMvcFactory()
	{
		return $this->factory;
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
	public function callEmpty($route)
	{
		$context = $this->getMvcFactory()
						->createEmptyContext();
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
	 * @param	MvcContextInterface $context
	 * @return	MvcContextInterface
	 */
	public function callWithContext($routeKey, MvcContextInterface $context)
	{
		$dispatcher = $this->getDispatcher();
		
		$tmp = $this->getMvcFactory()
					->createContext($routeKey, $context->getInput());

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
	protected function setDispatcher(MvcDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param	MvcActionDispatcherInterface $dispatcher
	 * @return	null
	 */
	protected function setMvcFactory(MvcFactoryInterface $factory)
	{
		$this->factory = $factory;
	}
}
