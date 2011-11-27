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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\ContextInterface;

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
			$pre = new FilterChain('pre');
		}
		if ('pre' !== $pre->getType()) {
			throw new InvalidArgumentException(
				"First param is a pre not post filter chain"
			);
		}
		$this->pre = $pre;

		if (null === $post) {
			$post = new FilterChain('post');
		}

		if ('post' !== $post->getType()) {
			throw new InvalidArgumentException(
				"Second param is a post not pre filter chain"
			);
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
			throw new InvalidArgumentException(
				"param given must be a string or array"
			);
		}

		$filters = array_reverse($filters);
		$target  = null;
		$preChain  = $this->getPreChain();
		$postChain = $this->getPostChain();
		foreach ($filters as $index => $class) {
			if (empty($class) || ! is_string($class)) {
				throw new RunTimeException(
					"Invalid filter given at index -($index)"
				);
			}
			$filter = new $class();
			if (! $filter instanceof InterceptingFilterInterface) {
				throw new RunTimeException(
					"Filter does not implment interface"
				);
			}

			$type = $filter->getType();
			if ('pre' === $type) {
				$preChain->addFilter($filter);
			}
			else if ('post' === $type) {
				$postChain->addFilter($filter);
			}
			else {
				throw new RunTimeException(
					"Filter type does not match pre or post"
				);
			}
		}

		return $this;
	}

	/**
	 * @param	InterceptingFilterInterface $filter
	 * @return	FilterManager
	 */
	public function addFilter(InterceptingFilterInterface $filter)
	{
		$type = $filter->getType();
		if ('pre' === $filter->getType()) {
			$chain = $this->getPreChain();
		}
		else {
			$chain = $this->getPostChain();
		}

		$chain->addFilter($filter);
		return $this;	
	}

	/**
	 * @param	ContextInterface
	 * @return	ContextInterface
	 */
	public function applyPreFilters(ContextInterface $context)
	{
		return $this->applyChain($this->getPreChain(), $context);
	}

	/**
	 * @param	ContextInterface
	 * @return	ContextInterface
	 */
	public function applyPostFilters(ContextInterface $context)
	{
		return $this->applyChain($this->getPostChain(), $context);
	}

	/**
	 * @param	FilterChainInterface $chain
	 * @param	ContextInterface $context
	 * @return	ContextInterface
	 */
	public function applyChain(FilterChainInterface $chain, 
								  ContextInterface $context)
	{
		if (! $chain->hasFilters()) {
			return $context;
		}
		
		$result = $chain->apply($context);
		if ($result instanceof ContextInterface) {
			$contexrt = $result;
		}
	
		return $context;
	}
}
