<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException;

/**
 */
class RouteIntercept implements RouteInterceptInterface
{
	/**
	 * List of intercept filters used before the action is processed
	 * @var array
	 */
	protected $preFilters = array();

	/**
	 * List of pre intercept filters to be exclude 
	 * @var array
	 */
	protected $excludedPreFilters = array();

	/**
	 * Framework will skip all pre filters if this is true
	 * @var bool
	 */
	protected $isPreFilter = false;

	/**
	 * List of intercept filters used after the action is processed
	 * @var array
	 */
	protected $postFilters = array();

	/**
	 * List of post intercept filters to be exclude 
	 * @var array
	 */
	protected $excludedPostFilters = array();

	/**
	 * Framework will skip all post filters if this is true
	 * @var bool
	 */
	protected $isPostFilter = false;

	/**
	 * @return	RouteInterceptChain
	 */
	public function enablePreFiltering()
	{
		$this->isPreFilter = true;
		return $this;
	}

	/**
	 * @return	RouteInterceptChain
	 */
	public function disablePreFiltering()
	{
		$this->isPreFilter = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPreFilteringEnabled()
	{
		return $this->isPreFilter;
	}

	/**
	 * @return	array
	 */
	public function getPreFilters()
	{
		return $this->preFilters;
	}

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setPreFilters(array $list)
	{
		if (! $this->isValidFilterList($list)) {
			$err = "pre intercept filter must be a non empty string";
			throw new DomainException($err);
		}

		$this->preFilters = $list;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPreFilters()
	{
		return ! empty($this->preFilters);
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters()
	{
		return ! empty($this->excludedPreFilters);
	}

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters()
	{
		return $this->excludedPreFilters;
	}

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setExcludedPreFilters(array $list)
	{
		if (! $this->isValidFilterList($list)) {
			$err = "pre intercept filter must be a non empty string";
			throw new DomainException($err);
		}

		$this->excludedPreFilters = $list;
		return $this;	
	}

	/**
	 * @return	RouteInterceptChain
	 */
	public function enablePostFiltering()
	{
		$this->isPostFilter = true;
		return $this;
	}

	/**
	 * @return	RouteInterceptChain
	 */
	public function disablePostFiltering()
	{
		$this->isPostFilter = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPostFilteringEnabled()
	{
		return $this->isPostFilter;
	}

	/**
	 * @return	array
	 */
	public function getPostFilters()
	{
		return $this->postFilters;
	}

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setPostFilters(array $list)
	{
		if (! $this->isValidFilterList($list)) {
			$err = "post intercept filter must be a non empty string";
			throw new DomainException($err);
		}

		$this->postFilters = $list;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPostFilters()
	{
		return ! empty($this->postFilters);
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters()
	{
		return ! empty($this->excludedPostFilters);
	}

	/**
	 * @return	array
	 */
	public function getExcludedPostFilters()
	{
		return $this->excludedPostFilters;
	}

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setExcludedPostFilters(array $list)
	{
		if (! $this->isValidFilterList($list)) {
			$err = "post intercept filter must be a non empty string";
			throw new DomainException($err);
		}

		$this->excludedPostFilters = $list;
		return $this;	
	}

	/**
	 * @param	array	$list
	 * @return	bool
	 */
	protected function isValidFilterList(array $list)
	{
		foreach ($list as $filter) {
			if (! is_string($filter) || empty($filter)) {
				return false;
			}
		}

		return true;
	}
}
