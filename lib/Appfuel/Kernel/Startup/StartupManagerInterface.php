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
	InvalidArgumentException;

/**
 * Startup tasks are strategies used to hold decrete sets of initialization
 * logic. You can define the fully qualified class for a startup in the config
 * or route detail. This manager will run a list of startup tasks or just a 
 * single one.
 */
interface StartupManagerInterface
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
	 * @param	StartupTaskInterface $task
	 * @param	array	$manual
	 * @return	null
	 */
	public function runTask(StartupTaskInterface $task, array $manual = null);

    /**
	 * Startup tasks are strategies used to hold decrete sets of initialization
	 * logic.   
	 * @return	null
     */
	public function runTasks(array $list);
}
