<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\Env;

use Appfuel\Framework\Exception,
	Appfuel\Stdlib\Data\BagInterface,
	Appfuel\Stdlib\Data\Bag;

/**
 * Value object used to hold the current state of the frameworks environment 
 * settings.
 */
class State
{
	/**
	 * Used to hold the data which represents state properties 
	 * @return string
	 */
	protected $bag  = null;

	/**
	 * Key to be used to identify the various state settings
	 * @var array
	 */
	protected $keys = array(
		'include_path',
        'include_path_action',
        'display_errors',
        'error_reporting',
        'enable_autoloader',
        'default_timezone'
	);
	
	/**
	 * Validate if the data is an array or BagInterface and assign.
	 * 
	 * @param	mixed	$data
	 * @return	State
	 */
	public function __construct($data)
	{
		if (is_array($data)) {
			$this->bag = new Bag($data);
		}
		else if ($data instanceof BagInterface) {
			$this->bag = $data;
		} 
		else {
			$err = 'Invalid argument given to the constructor. ' .
				   'Must be an array or object that implements a ' .
				   'Appfuel\\Stdlib\\Data\\BagInterface';
			throw new Exception($err);
		} 
	}

	/**
	 * Generally used when you need to automate pulling these settings out
	 * of a array
	 * 
	 * @return	array
	 */
	public function getkeys()
	{
		return $this->keys;
	}

	/**
	 * Get any property in the bag if it exists, return default when 
	 * it doen't
	 *
	 * @param	string	$name
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function get($name, $default = null)
	{
		return $this->getBag()
					->get($name, $default);
	}

	/**
	 * Check if the property is located in the bag
	 * 
	 * @param	string	$name
	 * @return	bool
	 */
	public function exists($name) 
	{
		return $this->getBag()
					->exists($name);
	}

	/**
	 * @return bool
	 */
	public function isErrorConfiguration()
	{
        return $this->exists('display_errors') ||
			   $this->exists('error_reporting');
	}

	/**
	 * @return string
	 */
	public function displayErrors()
	{
		return $this->get('display_errors', null);
	}

	/**
	 * @return string
	 */
	public function errorReporting()
	{
		return $this->get('error_reporting', null);
	}

	/**
	 * @return	bool
	 */
	public function isTimezoneConfiguration()
	{
		return $this->exists('default_timezone');
	}

	/**
	 * @return string
	 */
	public function defaultTimezone()
	{
		return $this->get('default_timezone', null);
	}

	/**
	 * @return bool
	 */
	public function isIncludePathConfiguration()
	{
		return $this->exists('include_path');
	}

	/**
	 * @return string
	 */
	public function includePath()
	{
		return $this->get('include_path', null);
	}

	/**
	 * @return 
	 */
	public function includePathAction()
	{
		return $this->get('include_path_action', null);
	}

	/**
	 * @return bool
	 */
	public function isRestoreAutoloaders()
	{
		$loaders = $this->get('autoload_stack', false);
		if (! is_array($loaders)) {
			return false;
		}

		return true;
	}

	/**
	 * @return	bool
	 */
	public function isEnableAutoloader()
	{
		$isEnable = $this->get('enable_autoloader', false);
		if ($isEnable) {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function autoloadStack()
	{
		return $this->get('autoload_stack', null);
	}

	/**
	 * @return	BagInterface
	 */
	protected function getBag()
	{
		return $this->bag;
	}
}
