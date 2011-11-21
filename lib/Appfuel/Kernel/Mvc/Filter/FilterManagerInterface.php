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


use Appfuel\Kernel\Mvc\ContextInterface;

/**
 */
interface FilterManagerInterface
{
	public function getPostChain();
	public function getPreChain();
	public function loadFilters($filters);
	public function addFilter(InterceptingFilterInterface $filter);
	public function applyPreFilters(ContextInterface $context);
	public function applyPostFilters(ContextInterface $context);
    public function applyChain(FilterChainInterface $fc, ContextInterface $ct);
}
