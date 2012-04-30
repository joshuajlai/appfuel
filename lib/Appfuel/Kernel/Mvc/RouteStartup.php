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

use DomainException;

/**
 * Controls which tasks are applied during startup
 */
class RouteStartup implements RouteStartupInterface
{
	/**
	 * The framework should prepend these tasks to the startup task list
	 * @var bool
	 */
	protected $isPrepend = false;

	/**
	 * The framework should ignore any startup tasks defined in the config
	 * @var bool
	 */
	protected $isIgnoreConfig = false;

	/**
	 * Determines is startup tasks should be run
	 * @var bool
	 */
	protected $isStartupDisabled = false;

	/**
	 * List of task class names which are used during application startup
	 * @var array
	 */
	protected $tasks = array();

	/**
	 * List of tasks defined in the config that should be exclude from the
	 * final task list
	 * @var array
	 */
	protected $excludedTasks = array();

	/**
	 * @return	RouteStartup
	 */
	public function prependStartupTasks()
	{
		$this->isPrepend = true;
		return $this;
	}

	/**	
	 * @return	RouteStartup
	 */
	public function appendStartupTasks()
	{
		$this->isPrepend = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPrependStartupTasks()
	{
		return $this->isPrepend;
	}

	/**
	 * @return	RouteStartup
	 */
	public function ignoreConfigStartupTasks()
	{
		$this->isIgnoreConfig = true;
		return $this;
	}

	/**
	 * @return	RouteStartup
	 */
	public function useConfigStartupTasks()
	{
		$this->isIgnoreConfig = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isIgnoreConfigStartupTasks()
	{
		return $this->isIgnoreConfig;
	}

	/**
	 * @return	RouteStartup
	 */
	public function enableStartup()
	{
		$this->isStartupDisabled = false;
		return $this;
	}

	/**
	 * @return	RouteStartup
	 */
	public function disableStartup()
	{
		$this->isStartupDisabled = true;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isStartupDisabled()
	{
		return $this->isStartupDisabled;
	}

	/**
	 * @return	bool
	 */
	public function isStartupTasks()
	{
		return ! empty($this->tasks);
	}

	/**
	 * @return	array
	 */
	public function getStartupTasks()
	{
		return $this->tasks;
	}

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setStartupTasks(array $list)
	{
		if (! $this->isValidTaskList($list)) {
			$err = "startup tasks must be non empty strings";
			throw new DomainException($err);
		}

		$this->tasks = $list;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks()
	{
		return ! empty($this->excludedTasks);
	}

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks()
	{
		return $this->excludedTasks;
	}

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setExcludedStartupTasks(array $list)
	{
		if (! $this->isValidTaskList($list)) {
			$err = "startup tasks must be non empty strings";
			throw new DomainException($err);
		}

		$this->excludedTasks = $list;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	bool
	 */
	protected function isValidTaskLIst(array $list)
	{
		foreach ($list as $task) {
			if (! is_string($task) || empty($task)) {
				return false;
			}
		}

		return true;
	}
}
