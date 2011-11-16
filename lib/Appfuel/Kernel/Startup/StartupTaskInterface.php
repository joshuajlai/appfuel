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

/**
 * The startup task is used to initialize on or more subsystems. It occurs
 * when the kernel is initialized and you register the startup task's class
 * in the config file. The kernel initializer will create the task (so its
 * important to have a constructor that can work with no parameters) and
 * use its iterface 
 */
interface StartupTaskInterface
{

	/**
	 * List of keys to pull out of the registry
	 *
	 * @return	null|string|array
	 */
	public function getRegistryKeys();

	/**
	 * Execute initialization with parameters taken from the Kernel Registry
	 * 
	 * @param	array	$params
	 * @return	null
	 */
	public function execute(array $params);

	/**
	 * Reports the result of the initialization
	 *
	 * @return	string
	 */
	public function getStatus();
}	
