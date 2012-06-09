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

interface InterceptChainInterface
{
	/**
	 * @return	array
	 */
	public function getFilters();

	/**
	 * @param	array	$filters
	 * @return	InterceptChain
	 */
	public function setFilters(array $filters);

	/**
	 * @param	InterceptFilterInterface
	 * @return	InterceptChain
	 */
	public function addFilter(InterceptFilterInterface $filter);

	/**
	 * @param	array	$filters
	 * @return	InterceptChain
	 */
	public function loadFilters(array $filters);

	/**
	 * @return	InterceptChain
	 */
	public function clearFilters();

	/**
	 * @return bool
	 */
	public function isFilters();

	/**
	 * @param	MvcContextInterface	 $context
	 * @return	MvcContextInterface
	 */
	public function applyFilters(MvcContextInterface $context);
}
