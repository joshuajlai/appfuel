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
namespace Appfuel\Kernel\Startup;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry;

/**
 */
class ConfigHandler implements ConfigHandlerInterface
{
	/**
	 * List of status messages reported by the startup task
	 * @param	string
	 */
	static protected $statusList = array();

	/**
	 * @return	array
	 */
	static public function getStatusList()
	{
		return self::$statusList;
	}

	/**
	 * @param	string	$key
	 * @param	string	$msg
	 * @return	null
	 */
	static public function addStatus($key, $msg)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'status key must be a non empty string';
			throw new InvalidArgumentException($err);	
		}

		if (! is_string($msg) || empty($msg)) {
			$err = 'status message must be a non empty string';
			throw new InvalidArgumentException($msg);
		}

		self::$statusList[$key] = $msg;
	}

	/**
	 * @param	string	$key
	 * @retunn	string | false 
	 */
	public function getStatus($key)
	{
		if (! is_string($key) || ! isset(self::$statusList[$key])) {
			return false;
		}

		return self::$statusList[$key];
	}

	/**
	 * @return	array
	 */
	public function getTasksFromRegistry()
	{
		return ConfigRegistry::getParam('tasks-tasks', array());
	}

	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function collectFromRegistry(array $list)
	{
		return ConfigRegistry::collectParams($list);
	}

	/**
	 * @param	StartupTaskInterface $task
	 * @param	array	$manual
	 * @return	null
	 */
	public function runTask(StartupTaskInterface $task, array $params)
	{
		$task->execute($params);
	}

    /**
	 * Startup tasks are strategies used to hold decrete sets of initialization
	 * logic.   
	 * @return	null
     */
	public function runTasks(array $list)
	{
		$params = null;
		foreach ($list as $item) {
			if (is_string($item)) {
				$task = new $item();
			}
			else if (is_array($item) && 2 === count($item)) {
				$task = current($item);
				if (is_string($task)) {
					$task = new $task();
				}
				$params = next($item);
				if (! is_array($params)) {
					$params = null;
				}
				
			}
			else if ($item instanceof StartupTaskInterface) {
				$task = $item;
			}
			else {
				$err  = "could not run task: item in the list was not ";
				$err .= "a string or an array or an object that implements ";
				$err .= "Appfuel\Kernel\Startup\StartupTaskInterface";
				throw new LogicException($err);
			}

			if (null === $params) {
				$params = $this->collectFromRegistry($task->getRegistryKeys());
			}

			$this->runTask($task, $params);
		}
	}
}
