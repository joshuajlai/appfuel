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
 */
interface MvcRouteHandlerInterface
{
	/**
	 * @return	string
	 */
	public function getMasterKey();

	/**
	 * @return	MvcRouteDetailInterface
	 */
	public function getMasterDetail();

	/**
	 * @param	string $key
	 * @return	bool
	 */
	public function isValidKey($key);

	/**
	 * @param	string	$key
	 * @return	null | MvcRouteDetailInterface
	 */
	public function getRouteDetail($key);
}
