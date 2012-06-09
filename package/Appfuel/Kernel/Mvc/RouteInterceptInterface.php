<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

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
