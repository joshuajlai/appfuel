<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
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
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findAction($method = null);

	/**
	 * @return	string
	 */
	public function getName();

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	public function setName($name);

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
