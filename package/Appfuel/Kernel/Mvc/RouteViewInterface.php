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

use InvalidArgumentException;

/**
 */
interface RouteViewInterface
{
	/**
	 * @return string
	 */
	public function getFormat();

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setFormat($name);

	/**
	 * @return	RouteView
	 */
	public function disableView();

	/**
	 * @return	RouteView
	 */
	public function enableView();

	/**
	 * @return	bool
	 */
	public function isViewDisabled();

	/**
	 * @return	RouteView
	 */
	public function enableManualView();

	/**
	 * @return	RouteView
	 */
	public function disableManualView();

	/**
	 * @return	bool
	 */
	public function isManualView();

	/**
	 * @return	bool
	 */
	public function isViewPackage();

	/**
	 * @return	string
	 */
	public function getViewPackage();

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setViewPackage($name);
	
	/**
	 * @return RouteView
	 */
	public function clearViewPackage();
}
