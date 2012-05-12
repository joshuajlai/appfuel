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

/**
 */
interface RouteInterceptInterface
{
	/**
	 * @return	RouteInterceptChain
	 */
	public function enablePreFiltering();

	/**
	 * @return	RouteInterceptChain
	 */
	public function disablePreFiltering();

	/**
	 * @return	bool
	 */
	public function isPreFilteringEnabled();

	/**
	 * @return	array
	 */
	public function getPreFilters();

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setPreFilters(array $list);

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters();

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters();

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setExcludedPreFilters(array $list);

	/**
	 * @return	RouteInterceptChain
	 */
	public function enablePostFiltering();

	/**
	 * @return	RouteInterceptChain
	 */
	public function disablePostFiltering();

	/**
	 * @return	bool
	 */
	public function isPostFilteringEnabled();

	/**
	 * @return	array
	 */
	public function getPostFilters();

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setPostFilters(array $list);

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters();

	/**
	 * @return	array
	 */
	public function getExcludedPostFilters();

	/**
	 * @param	array	$list
	 * @return	RouteInterceptChain
	 */
	public function setExcludedPostFilters(array $list);
}
