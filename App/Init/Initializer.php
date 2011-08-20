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
namespace Appfuel\App\Init;

use Appfuel\Framework\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\App\Init\TaskInterface,
	Appfuel\Framework\App\Init\InitializerInterface,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * The initializer looks into the registry for the init keys (names used by
 * the initializer to map to class names) used to create init items that 
 * handle the concrete logic of initialization.
 */
class Initializer implements InitializerInterface
{
	/**
	 * Used to translate task names to class names to be created
	 * @var array
	 */
	protected $map = array(
		'system' => 'SystemTask',
		'db'	 => 'DbTask'
	);

    /**  
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	public function initialize()
	{
		$error = "Invalid task create:";
		$tasks = Registry::get('init-tasks', array());
		foreach ($tasks as $name) {
			$task = $this->createTask($name);
			if (! $task instanceof TaskInterface) {
				$error .= "$name must implement ";
				$error .= "Appfuel\Framework\Init\\TaskInterface";
				throw new Exception($error);
			}
			
			$task->init();
			
		}
	}

	/**
	 * @param	string	$name
	 * @return	TaskInterface | false 
	 */
	protected function createTask($name)
	{
		if (empty($name) || ! is_string($name) || ! isset($this->map[$name])) {
			return false;
		}

		$class = __NAMESPACE__ . "\\{$this->map[$name]}";
		return new $class();
	}
}	
