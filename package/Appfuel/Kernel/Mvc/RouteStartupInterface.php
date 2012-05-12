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
    Appfuel\DataStructure\Dictionary;

/**
 * Allows control over which tasks are applied during application startup.
 * Note: this does not include control over framework tasks. Tasks may be
 * added or excluded. All tasks or just tasks defined in the config files
 * may be disabled.
 */
interface RouteStartupInterface
{
	/**
	 * @return	RouteStartup
	 */
	public function prependStartupTasks();

	/**	
	 * @return	RouteStartup
	 */
	public function appendStartupTasks();

	/**
	 * @return	bool
	 */
	public function isPrependStartupTasks();

	/**
	 * @return	RouteStartup
	 */
	public function ignoreConfigStartupTasks();

	/**
	 * @return	RouteStartup
	 */
	public function useConfigStartupTasks();

	/**
	 * @return	bool
	 */
	public function isIgnoreConfigStartupTasks();

	/**
	 * @return	RouteStartupInterface
	 */
	public function enableStartup();

	/**
	 * @return	RouteStartupInterface
	 */
	public function disableStartup();

	/**
	 * @return	bool
	 */
	public function isStartupDisabled();

	/**
	 * @return	bool
	 */
	public function isStartupTasks();

	/**
	 * @return	array
	 */
	public function getStartupTasks();

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setStartupTasks(array $list);

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks();

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks();

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setExcludedStartupTasks(array $list);
}
