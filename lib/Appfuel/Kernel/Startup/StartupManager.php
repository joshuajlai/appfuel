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
 * Startup tasks are strategies used to hold decrete sets of initialization
 * logic. You can define the fully qualified class for a startup in the config
 * or route detail. This manager will run a list of startup tasks or just a 
 * single one.
 */
class StartupManager implements StartupManagerInterface
{
	/**
	 * @return	array
	 */
	public function getTasksFromRegistry()
	{
		return KernelRegistry::getParam('startup-tasks', array());
	}

	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function collectFromRegistry(array $list)
	{
		return KernelRegistry::collectParams($list);
	}

	/**
	 * @param	StartupTaskInterface $task
	 * @param	array	$manual
	 * @return	null
	 */
	public function runTask(StartupTaskInterface $task, array $manual = null)
	{
		if (null === $manual) {
			$params = $this->collectFromRegistry($task->getRegistryKeys());
		}
		else {
			$params = $manual;
		}

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

			$this->runTask($task, $params);
		}
	}
}
