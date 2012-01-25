<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc\Filter;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * The filter manager is a facade which handles the usage of intercepting
 * filters. Unlike the traditional intercepting filter that forms a chain 
 * where each filter has the next filter. We hold the filters in two simple
 * arrays. 
 * 
 *	Pre		holds all the filters to be loaded before the action controller
 *			is executed. 
 *	Post	holds all the filters to be loaded after the action controller
 *			has executed but before the content is outputted.
 */
class FilterManager implements FilterManagerInterface
{
	/**
	 * Pre Filter Chain
	 * @var FilterChain
	 */
	protected $pre = null;

	/**
	 * Post Filter Chain 
	 * @var FilterChain
	 */
	protected $post = null;

	/**
	 * @param	FilterChainInterface	$pre
	 * @param	FitlerChainInterface	$post
	 * @return	FilterManager
	 */
	public function __construct(FilterChainInterface $pre = null, 
								FilterChainInterface $post = null)
	{
		if (null === $pre) {
			$pre = new FilterChain();
		}
		$this->pre = $pre;

		if (null === $post) {
			$post = new FilterChain();
		}
		$this->post = $post;
	}

	/**
	 * @return	FilterChainInterface
	 */
	public function getPreChain()
	{
		return $this->pre;
	}

	/**
	 * @return	FilterChainInterface
	 */
	public function getPostChain()
	{
		return $this->post;
	}

	/**
	 * Load a list of filters given as class names
	 *
	 * @param	string | array	$filters
	 * @return	FilterManager
	 */
	public function loadFilters($filters)
	{
		if (empty($filters)) {
			return $this;
		}

		if (is_string($filters)) {
			$filters = array($filters);
		}
		else if (! is_array($filters)) {
			$err = 'filters must be a string or an array of strings';
			throw new InvalidArgumentException($err);
		}

		$filters = array_reverse($filters);
		foreach ($filters as $index => $class) {
			if (empty($class) || ! is_string($class)) {
				$err = "Invalid filter given at index -($index)";
				throw new LogicException($err);
			}
			$filter = new $class();
			if (! $filter instanceof InterceptFilterInterface) {
				$err = "Filter does not implement interface";
				throw new LogicException($err);
			}

			$this->addFilter($filter);	
		}

		return $this;
	}

	/**
	 * @param	InterceptingFilterInterface $filter
	 * @return	FilterManager
	 */
	public function addFilter(InterceptFilterInterface $filter)
	{
		if ($filter instanceof PreInterceptFilterInterface) {
			$chain = $this->getPreChain();
		}
		else if ($filter instanceof PostInterceptFilterInterface) {
			$chain = $this->getPostChain();
		}
		else {
			$err  = 'Filter interface is not supported must be a ';
			$err .= 'PreInterceptFilterInterface or ';
			$err .= 'PostInterceptFilterInterface';
			throw new LogicException($err);
		}
		
		$chain->addFilter($filter);
		return $this;	
	}

	/**
	 * @param	ContextInterface
	 * @return	ContextInterface
	 */
	public function applyPreFilters(MvcContextInterface $context)
	{
		return $this->applyChain($this->getPreChain(), $context);
	}

	/**
	 * @param	ContextInterface
	 * @return	ContextInterface
	 */
	public function applyPostFilters(MvcContextInterface $context)
	{
		return $this->applyChain($this->getPostChain(), $context);
	}

	/**
	 * apply all filters down the chain. Returning a context indicates that
	 * its a new context that should be used inplace of the one passed in
	 *
	 * @param	FilterChainInterface $chain
	 * @param	ContextInterface $context
	 * @return	ContextInterface | null 
	 */
	public function applyChain(FilterChainInterface $chain, 
							   MvcContextInterface  $context)
	{
		if (! $chain->hasFilters()) {
			return;
		}
	
		return $chain->apply($context);
	}
}
