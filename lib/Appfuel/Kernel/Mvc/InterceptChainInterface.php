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

/**
 */
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
