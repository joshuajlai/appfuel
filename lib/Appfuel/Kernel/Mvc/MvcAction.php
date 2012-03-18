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

use Appfuel\Orm\OrmRepositoryInterface;

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
	 * @var	array
	 */
	protected $repoList = array();

	/**
	 * @param	string	$route
	 * @return	MvcAction
	 */
	public function __construct(MvcDispatcherInterface $dispatcher = null,
								MvcContextBuilderInterface $builder = null,
								array $params = null)
	{
		if (null === $dispatcher) {
			$dispatcher = new MvcDispatcher();
		}
		$this->setDispatcher($dispatcher);

		if (null === $builder) {
			$builder = new MvcContextBuilder();
		}
		$this->setContextBuilder($builder);
		$this->initialize($params);
	}

	/**
	 * Used to intialize domain repositories needed by the mvc action
	 *
	 * @param	array	$params		null
	 * @return	MvcAction
	 */
	public function initialize(array $params = null)
	{	
        if (! isset($params['domains']) || ! is_array($params['domains'])) {
            return $this;
        }

        $domains = $params['domains'];
        foreach ($domains as $key => $factoryClass) {
            if (! is_string($factoryClass) || empty($factoryClass)) {
                $err = 'domain repo factory class must be non empty string';
                throw new InvalidArgumentException($err);
            }

            $factory = new $factoryClass();
            $repo = $factory->createRepository($key);
            $this->addRepository($key, $repo);
        }

		return $this;
	}

	public function isRepository($key)
	{
		if (! is_string($key) || ! isset($this->repoList[$key])) {
			return false;
		}

		return $this->repoList[$key] instanceof OrmRepositoryInterface;
	}

	public function addRepository($key, OrmRepositoryInterface $repo)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'domain repository key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->repoList[$key] = $repo;
		return $this;
	}

	public function getRepository($key)
	{
		if (! $this->isRepository($key)) {
			return false;
		}

		return $this->repoList[$key];
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
	public function callWithNoInputs($route)
	{

		$context = $this->getContextBuilder()
						->clear()
						->setRoute($route)
						->noInputRequired()
						->build();

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
	public function callAsContext($routeKey, MvcContextInterface $context)
	{
		$dispatcher = $this->getDispatcher();

		$tmp = $this->getContextBuilder()
					->setRouteKey($routeKey)
					->setInput($context->getInput())
					->build();

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
	protected function setContextBuilder(MvcContextBuilderInterface $builder)
	{
		$this->contextBuilder = $builder;
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
