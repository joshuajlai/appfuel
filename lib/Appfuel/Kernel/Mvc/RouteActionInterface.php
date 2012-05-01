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

use DomainException,
	InvalidArgumentException;

/**
 */
interface RouteActionInterface
{
	/**
	 * @return	RouteAction
	 */
	public function enableStrict();

	/**
	 * @return	RouteAction
	 */
	public function disableStrict();

	/**
	 * @return bool
	 */
	public function isStrict();

	/**
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findActionName($method = null);

	/**
	 * @return	string
	 */
	public function getActionName();

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	public function setActionName($name);

	/**
	 * @param	string	$method
	 * @return	string | false
	 */
	public function getNameInMap($method);

	/**
	 * @return	array
	 */
	public function getMap();

	/**
	 * @param	array	$map
	 * @return	RouteAction
	 */
	public function setMap(array $map);
}
