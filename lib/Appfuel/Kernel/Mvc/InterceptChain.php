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
namespace Appfuel\Kernel\Mvc;

use Closure,
	InvalidArgumentException;

/**
 * Runs the intercept filters that have been added to the chain. Instead of
 * a link list where the filter is responsible for breaking the chain, the
 * chain asks the filter if it wants to break or replace the context or both.
 */
class InterceptChain implements InterceptChainInterface
{
	/**
	 * List of filters to be run
	 */
	protected $filters = array();

	/**
	 * @return	array
	 */
	public function getFilters()
	{
		return $this->filters;	
	}

	/**
	 * @param	array	$filters
	 * @return	InterceptChain
	 */
	public function setFilters(array $filters)
	{
		$this->clearFilters();
		$this->loadFilters($filters);
		return $this;
	}

	/**
	 * @param	InterceptFilterInterface
	 * @return	InterceptChain
	 */
	public function addFilter(InterceptFiterInterface $filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	/**
	 * @param	array	$filters
	 * @return	InterceptChain
	 */
	public function loadFilters(array $filters)
	{
		foreach ($filters as $filter) {
			$this->addFilter($filter);
		}

		return $this;
	}

	/**
	 * @return	InterceptChain
	 */
	public function clearFilters()
	{
		$this->filters = array();
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFilters()
	{
		return count($this->filters) > 0;
	}

	/**
	 * @param	MvcContextInterface	 $context
	 * @return	MvcContextInterface
	 */
	public function applyFilters(MvContextInterface $context)
	{
		if (! $this->isFilters()) {
			return $context;
		}

		$builder = $this->getContextBuilder();
		$filters = $this->getFilters();
		foreach ($filters as $filter) {
			$filter->apply($context, $builder);
			if ($filter->isReplaceContext()) {
				$context = $filter->getContextToReplace();
			}
		
			if ($filter->isBreakChain()) {
				break;
			}
		}

		return $context;
	}
}
