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

use InvalidArgumentException;

/**
 * The startup task is used to initialize on or more subsystems. It occurs
 * when the kernel is initialized and you register the startup task's class
 * in the config file. The kernel initializer will create the task (so its
 * important to have a constructor that can work with no parameters) and
 * use its iterface 
 */
abstract class StartupTaskAbstract implements StartupTaskInterface
{
	/**
	 * List of keys to be collected by the registry the keys are structured
	 * as label => default value.
	 * @var	 array
	 */
	protected $keys = null;

	/**
	 * Status text used to indicate the result of task initialization
	 * @var string
	 */
	protected $status = null;

	/**
	 * Reports the result of the initialization
	 *
	 * @return	string
	 */
	public function getStatus()
	{
		return $this->status;		
	}

	/**
	 * @param	string	$text
	 * @return	null
	 */
	public function setStatus($text)
	{
		if (! is_string($text)) {
			throw new InvalidArgumentException("status must be a string");
		}

		$this->status = $text;
	}

	/**
	 * List of keys to pull out of the registry
	 *
	 * @return	null|string|array
	 */
	public function getRegistryKeys()
	{
		return $this->keys;
	}

	/**
	 * @param	array	$keys
	 * @return	null
	 */
	public function setRegistryKeys(array $keys)
	{
		/* non associative arrays use the default as null */
		if ($keys === array_values($keys)) {
				foreach ($keys as $key) {
					$this->addRegistryKey($key);
				}
		}
		else {
			foreach ($keys as $key => $default) {
				$this->addRegistryKey($key, $default);
			}
		}
	}

	/**
	 * @param	string	$label	
	 * @param	mixed	$default
	 * @return	null
	 */
	public function addRegistryKey($label, $default = null)
	{
		if (empty($label) || ! is_string($label)) {
			$err = 'label must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->keys[$label] = $default;
	}
}	
