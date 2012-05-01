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
 * Value object used by the MvcViewBuilder and stored in the MvcRouteDetail
 */
interface MvcViewDetailInterface
{
	/**
	 * @return	bool
	 */
	public function isView();

	/**
	 * @return	string
	 */
	public function getStrategy();

	/**
	 * @return	array
	 */
	public function getParams();

	/**
	 * @return	bool
	 */
	public function getMethod();
}
