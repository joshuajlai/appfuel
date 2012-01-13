<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

/**
 * Used to hold details specific about a route. This object is 
 * implemented in the action namespace. When a route key is mapped by the
 * dispatcher is resolves this action namespace and whhen building a context the
 * dispatcher will look in namespace for a class called 'RouteDetail'.
 * The deails include the route key used, is the route public, what acl code
 * are allowed access and what intercepting filters to use.
 */
interface MvcRouteDetailInterface
{
	/**
	 * Flag used to detemine if any acl checks need to be applied
	 * @return	bool
	 */
	public function isPublic();

	/**
	 * Determine is a given acl code is allowed to dispatch this route. 
	 * 
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAllowed($code);

	/**
	 * List of intercepting filters to be used specifically for this route
	 * @return	array
	 */
	public function getInterceptingFilters();
}
