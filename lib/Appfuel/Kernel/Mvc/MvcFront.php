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
	 * @param	MvcActionFactoryInterface	$factory
	 * @return	AppContext
	 */
	public function __construct(MvcDispatcherInterface $dispatcher,
								InterceptChainInterface $preChain,
								InterceptChainInterface $postChain)
	{
		$this->dispatcher = $dispatcher;
		$this->preChain	  = $preChain;
		$this->postChain  = $postChain;
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
	 *  
	 * @param	string	$strategy	console|ajax|htmlpage
	 * @return	int
	 */
	public function run(MvcContextInterface $context)
	{
		$routeKey = $context->getRouteKey();
		/*
		 * Mark this as the current route. Allows you to tell the difference
		 * between the initial route and one called by an mvc action
		 */
		$this->setCurrentRoute($routeKey);
		$detail = $this->getRouteDetail($routeKey);

		if (! $detail->isSkipPreFilters()) {
			$context = $this->runPreFilters($detail, $context);
		}

		/*
		 * Only dispatch a context if its exit code is within the range of 
		 * success. Note console and html, ajax and api all follow http status
		 * codes.
		 */
		$exitCode = $context->getExitCode();
		if ($exitCode >= 200 && $exitCode < 300) {
			$dispatcher = $this->getDispatcher();
			$dispatcher->dispatch($context);

			/*
			 * PreFilters have the ability to change the current route
			 * so we grab it again just incase 
			 */
			$tmpRouteKey = $context->getRouteKey();
			if ($tmpRouteKey !== $routeKey) {
				$this->setCurrentRoute($tmpRouteKey);
				$detail = $this->getRouteDetail($tmpRouteKey);
			}

			if (! $detail->isSkipPostFilters()) {
				$context = $this->runPostFilters($detail, $context);
			}
		}

		return $context;
	}

	/**
	 * @param	MvcContextInterface		$context
	 * @return	MvcContextInterface
	 */
	public function runPreFilters(MvcRouteDetailInterface $detail,
								  MvcContextInterface $context)
	{
		$chain  = $this->getPreChain();

		if ($detail->isExcludedPreFilters()) {
			$chain->removeFilters($detail->getExcludedPreFilters());
		}

		if ($detail->isPreFilters()) {
			$chain->loadFilters($detail->getPreFilters());	
		}

		return $chain->applyFilters($context);
	}

	/**
	 * @param	MvcContextInterface		$context
	 * @return	MvcContextInterface
	 */
	public function runPostFilters(MvcRouteDetailInterface $detail,
								   MvcContextInterface $context)
	{
		$chain  = $this->getPostChain();

		if ($detail->isExcludedPostFilters()) {
			$chain->removeFilters($detail->getExcludedPostFilters());
		}

		if ($detail->isPostFilters()) {
			$chain->loadFiltrs($detail->getPostFilters());	
		}

		return $chain->applyFilters($context);
	}

	/**
	 * @return	MvcRouteDetail
	 */
	protected function getRouteDetail($key)
	{
		return MvcRouteManager::getRouteDetail($key);
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	protected function setCurrentRoute($key)
	{
		MvcRouteManager::setCurrentRouteKey($key);
	}
}
