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

use Appfuel\DataStructure\DictionaryInterface;

/**
 */
interface MvcRouteDetailInterface extends DictionaryInterface
{
	/**
	 * @return	bool
	 */
	public function isPublicAccess();

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess();

	/**
	 * @return bool
	 */
	public function isIgnoreAcl();

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($codes);

	/**
	 * @return	bool
	 */
	public function isPreFilteringEnabled();

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
	public function isPostFilteringEnabled();

	/**
	 * @return	bool
	 */
	public function isPostFilters();

	/**
	 * @return array
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
	public function isIgnoreConfigStartupTasks();

	public function isPrependStartupTasks();

	/**
	 * @return	bool
	 */
	public function isStartupTasks();

	/**
	 * @return	array
	 */
	public function getStartupTasks();

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks();

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks();

	/**
	 * @return	bool
	 */
	public function isViewDisabled();

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
	 * @return	string
	 */
	public function getActionName();
}
