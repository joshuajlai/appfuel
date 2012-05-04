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

use Appfuel\Orm\OrmManager;

/**
 * The mvc action is the controller in mvc. The front controller always 
 * dispatches a context to be processed by the mvc action based on a 
 * route (obtained via request uri, generally) that maps to that mvc action.
 * Every mvc action can also dispatch calls (process context) to any other
 * mvc action based on route (and context building), which always mvc actions
 * to be used rather than duplicated. 
 */
interface MvcActionInterface
{
	/**
	 * @param	string	$key
	 * @return	OrmRepositoryInterface
	 */
	public function getRepository($key, $source = 'db');

	/**
	 * @param	MvcActionDispatcher
	 * @return	null
	 */
	public function getDispatcher();

	/**
	 * @return 	MvcContextBuilder
	 */
	public function getMvcFactory();

	/**
	 * Must be implemented by concrete class
	 *
	 * @param	AppContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context);

	/**
	 * Makes it appear as if the context was used in the action you just 
	 * called. 
	 * 
	 * @param	string	$routeKey
	 * @param	MvcContextInterface $context
	 * @return	MvcContextInterface
	 */
	public function callWithContext($routeKey, MvcContextInterface $context);
}
