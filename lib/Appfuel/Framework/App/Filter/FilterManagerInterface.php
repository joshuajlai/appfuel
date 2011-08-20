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
namespace Appfuel\Framework\App;


use Appfuel\Framework\App\ContextInterface;

/**
 * Filter Manager is reponsible for adding filters and the filters. Filters
 * can run in one of two locations, either pre (before the controller action
 * is executed) and post (after the controller action has been executed). 
 */
interface FilterManagerInterface
{
	/**
	 * Used to determine if any filters experienced any errors
	 * 
	 * @return	bool
	 */
	public function isError();
		
	/**
	 * Object used to describe the error the filter encountered 
	 * 
	 * @return	Error
	 */
	public function getError();

	/**
	 * The manager implements a push down stack. First in last out.
	 *
	 * $param	string				$type	either pre or post
	 * @param	ContextInterface	$context
	 * @return	FilterManager
	 */
	public function addFilter($type, FilterItemInterface $filter);

	/**
	 * Run all filters registered as pre with the given context
	 * 
	 * @param	ContextInterface	$context	
	 * @return	mixed	bool | ContextInterface
	 */
	public function preProcess(ContextInterface $context);

	/**
	 * Run all filters registered as post with the given context
	 * 
	 * @param	ContextInterface	$context	
	 * @return	mixed	bool | ContextInterface
	 */
	public function postProcess(ContextInterface $context);

}
