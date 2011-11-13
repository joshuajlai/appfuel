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
namespace Appfuel\Kernel;

use Appfuel\Framework\Exception,

/**
 * The kernal intializer uses the kernal registry to get a list of start up
 * tasks. It will run through each task calling its execute methos. 
 */
class KernelInitializer implements KernelInitializerInterface
{
	/**
	 * Absolute path to the config file
	 * @var string
	 */
	protected $configPath = null;

	/**
	 * Each task has a status string which we save
	 * @var array
	 */
	static protected $status = array();

	/**
	 * Assign the config path
	 *
	 * @return	KernalInitializer
	 */
	public function __construct()
	{
		if (! defined('AF_BASE_PATH')) {
			$err = "base path constant -(AF_BASE_PATH) must be defined";
			throw new Exception($err);
		}

		$this->setConfigPath(AF_BASE_PATH . '/app/config/config.php');
	}

	/**
	 * Initialize will initialize the kernal registry and run the startup 
	 * tasks. when the $file parameter is empty it will use the appfuel 
	 * config path, when a string is given initialize will assume its the
	 * config it should use. When an array is given initialize will assume
	 * its the actual config data and use that.
	 *
	 * @param	string	$file
	 * @return	null
	 */
	public function initialize($file = null)
	{
		if (null === $file || is_string($file)) {
			$data = $this->getConfigData($file);
		}
		else if (! empty($file) && is_array($file)) {
			$data = $file;
		}

		$this->initializeKernelRegistry($data);
		$this->runStartupTasks();
	}

    /**  
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	public function runStartupTasks()
	{
		$tasks  = KernelRegistry::getStartupTasks();
		foreach ($tasks as $taskClass) {
			$task = new $taskClass();
			if (! ($task instanceof StartupTaskInterface)) {
				$err  = "task -($taskClass) does not implement Appfuel\Kernal";
				$err .= "\StartupTaskInterface";
				throw new Exception($err);
			}
	
			$task->execute();
			$statusResult = $task->getStatus();
			if (empty($statusResult) || ! is_string($statusResult)) {
				$statusResult = 'status not reported';
			}

			self::$status[$taskClass] = $statusResult;
		}
	}

	/**
	 * Make sure the config file exists and that it returns an associative
	 * array of config data. If no file path given the appfuel path is used
	 *
	 * @return	array
	 */
	public function getConfigData($file = null)
	{
		$err  = 'loading configuration failed';
		if (null === $file) {
			$file = $this->getConfigPath();
		}

		if (! file_exists($file)) {
			throw new Exception("config file not found at -($file)");
		}

		$data = require $file;
		if (! is_array($data) &&  $data === array_values($data)) {
			$err .= ' config file must return an associative array';
			throw new Exception($err);
		}
	
		return $data;	
	}

	/**
	 * @return null
	 */
	public function initializeKernelRegistry(array $data)
	{
		$err = 'loading configuration failed';
		if (isset($data['kernel-params']) && is_array($data['kernel-params'])) {
			KernelRegistry::setParams($data['kernel-params']);
		}

		if (isset($data['domain-map']) && is_array($data['domain-map'])) {
			KernelRegistry::setDomainMap($data['domain-map']);
		}

		if (isset($data['startup-tasks']) && is_array($data['startup-tasks'])){
			KernelRegistry::setStartupTasks($data['startup-tasks']);
		}
	}

	/**
	 * @return	string
	 */
	public function getConfigPath()
	{
		return $this->configPath;
	}

	/**
	 * @param	string	$path	absolute path to config file
	 * @return	null
	 */
	protected function setConfigPath($path)
	{
		if (empty($path) || !is_string($path)) {
			throw new Exception("config path must be a non empty string");
		}

		$this->configPath = $path;
	}
}	
