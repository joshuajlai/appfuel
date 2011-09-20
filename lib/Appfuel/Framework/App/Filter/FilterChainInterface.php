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
namespace Appfuel\Framework\App\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\ContextInterface,
	Appfuel\Framework\App\Filter\FilterChainInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * Filter chain is designed to hold filters of a particular type pre or post.
 * It also adds a filter but can not remove one once it is added and it can
 * apply start the filter chain
 */
interface FilterChainInterface
{
	/**
	 * @param	ContextInterface
	 * @return	mixed	Exception | ContextInterface
	 */
	public function apply(ContextInterface $context);

	/**
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function getHead();

	/**
	 * @param	InterceptingFilterInterface	$filter
	 * @return	InterceptingFilter
	 */
	public function setHead(InterceptingFilterInterface $filter);
	
	/**
	 * This will add the filter to head if there is not filter in head. 
	 * Otherwise it will set the filter in head to the next filter of the 
	 * filter passed and use the filter passed as head.
	 *
	 * @return	InterceptingFilterInterface | null when not set
	 */
	public function addFilter(InterceptingFilterInterface $filter);

	/**
	 * @return	string
	 */
	public function getType();
}
