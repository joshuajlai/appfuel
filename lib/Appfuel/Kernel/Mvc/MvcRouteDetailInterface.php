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
interface MvcRouteDetailInterface
{
	/**
	 * Flag used to detemine if any acl checks need to be applied
	 * @return	bool
	 */
	public function isPublicAccess();
	
	/**
	 * @return bool
	 */
	public function isInternalOnlyAccess();

	/**
	 * Determine is a given acl code is allowed to dispatch this route. 
	 * 
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($code);

	/**
	 * @return	bool
	 */
	public function isSkipPreFilters();

	/**
	 * @return	bool
	 */
	public function isPreFilters();

	/**
	 * @return	array
	 */
	public function getPreFilters();

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters();

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters();

	/**
	 * @return	bool
	 */
	public function isPostFilters();

	/**
	 * @return	array
	 */
	public function getPostFilters();

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters();

	/**
	 * @return array
	 */
	public function getExcludedPostFilters();

	/**
	 * @return	bool
	 */
	public function isSkipPostFilters();

	/**
	 * @return	bool
	 */
	public function isViewDetail();

	/**
	 * @return	array
	 */
	public function getViewDetail();
}
