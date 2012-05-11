<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * Runs a list tasks or an individual tasks. Task list and parameters are all
 * accessed from the configuration registry. 
 */
interface TaskHandlerInterface
{
	/**
	 * @return	array
	 */
	public function getTasksFromRegistry();

	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function collectFromRegistry(array $list);

	/**
	 * Used by the startup system to execute tasks from a list of class names
	 * stored in the configuration registry. The route and context is passed 
	 * into each task as way of injecting framework information into each task
	 * allowing the task to make decisions based on routing information or 
	 * user input (found in the context).
	 *
	 * @param	MvcRouteDetailInterface $route
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function kernelRunTasks(MvcRouteDetailInterface $route, 
								   MvcContextInterface $context);

	/**
	 * Collects data keys out of the configuration registry and uses them to
	 * execute the task. This is a way to run the task without needing to have
	 * access to the route or context.
	 *
	 * @param	StartupTaskInterface $task
	 * @return	null
	 */
	public function runTask(StartupTaskInterface $task);
}
