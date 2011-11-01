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
namespace Appfuel\Framework\Domain\Route;

use Appfuel\Framework\Orm\Domain\DomainModelInterface;

/**
 * A route binds the route key in the request uri to an action controller,
 * determines if that route is public and holds any intercepting filters that
 * need to be applied to the route
 */
interface RouteDomainInterface extends DomainModelInterface
{
	/**
	 * String used to identify the route
	 * @return	string
	 */
	public function getRouteKey();
	
	/**
	 * @throws	Appfuel\Framework\Exception		when empty or not a string
	 * @param	string	$key
	 * @return	RouteDomainInterface
	 */
	public function setRouteKey($key);


}
