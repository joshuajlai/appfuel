<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Config;

use InvalidArgumentException;

/**
 * Decouples the config settings located in the config files from that 
 * startup tasks that use them
 */
class ConfigRegistry
{
	/**
	 * List of configuration name value pairs
	 * @var Dictionary
	 */
	static protected $data = array();

	/**
	 * @return	array
	 */
	static public function getAll()
	{
		return	self::$data;
	}

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	static public function setAll(array $list)
	{
		self::clear();
		self::load($list);
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function load(array $list)
	{
		if (! empty($list) && $list === array_values($list)) {
			$err = "params must be an associative arrays";
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $key => $value) {
			self::add($key, $value);
		}
	}

	/**
	 * @return null
	 */
	static public function clear()
	{
		self::$data = array();
	}

	/**
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	null
	 */
	static public function add($key, $value)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'config key must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		self::$data[$key] = $value;
	}

	/**
	 * @param	string	$key
	 * @return	mixed | $default when not found
	 */
	static public function get($key, $default = null)
	{
		if (! self::exists($key)) {
			return $default;
		}

		return self::$data[$key];
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function exists($key)
	{
		if (! is_string($key) || ! array_key_exists($key, self::$data)) {
			return false;
		}

		return true;
	}
	
	/**
	 * Collect all the parameters in the list. List is an associative array
	 * of key => default. Where key is the config key we are looking for and
	 * default is the value we use when that key is not found. When default
	 * is 'af-exclude-not-found' then that key will be omitted from the 
	 * collection list.
	 *
	 * @param	array	$list
	 * @return	array
	 */
	static public function collect(array $list)
	{
		$result = array();
		foreach ($list as $key => $default) {
			$value = self::get($key, $default);
			if ('af-exclude-not-found' === $value) {
				continue;
			}
			$result[$key] = $value;
		}

		return $result;
	}
}
