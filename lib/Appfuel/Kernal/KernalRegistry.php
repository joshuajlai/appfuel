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
namespace Appfuel\Kernal;

use Appfuel\Framework\Exception;

/**
 * Decouples the kernal settings from the kernal
 */
class KernalRegistry
{
	/**
	 * List of kernal global kernal parameters used during intialization
	 * @var Dictionary
	 */
	static protected $params = array();

	/**
	 * Holds a list of domain key to domain class mappings
	 * @var array
	 */
	static protected $domains = array();

	/**
	 * List of tasks startup classes used to by the kernal intializer
	 * @var array
	 */
	static protected $startup = array();

	/**
	 * @param	array	$classes
	 * @return	null
	 */
	static public function setStartupTasks(array $classes) 
	{
		foreach ($classes as $class) {
			self::addStartupTask($class);
		}
	}

	/**
	 * @return	array
	 */
	static public function getStartupTasks()
	{
		return self::$startup;
	}

	/**
	 * A startup task is a fully qualified namespace for a startup strategy.
	 * The kernal intitializer will run all the startup tasks in this list
	 * 
	 * @param	string	$class
	 * @return	null
	 */
	static public function addStartupTask($class)
	{
		if (! self::isValidString($class)) {
			throw new Exception("Key must be a non empty scalar");
		}

		if (! in_array($class, self::$startup, true)) {
			self::$startup[] = trim($class);
		}
	}

	/**
	 * @return	null
	 */
	static public function clearStartupTasks()
	{
		self::$startup = array();
	}

	/**
	 * @return	array
	 */
	static public function getParams()
	{
		return	self::$params;
	}

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	static public function setParams(array $params)
	{
		if (! empty($params) && $params === array_values($params)) {
			throw new Exception("params must be an associative arrays");
		}

		foreach ($params as $key => $value) {
			self::addParam($key, $value);
		}
	}

	/**
	 * @return null
	 */
	static public function clearParams()
	{
		self::$params = array();
	}

	/**
	 * Parameters are strictly key value pairs and the key must be a string. 
	 * Integers have very little human readable value so they are discouraged
	 * 
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	null
	 */
	static public function addParam($key, $value)
	{
		if (! self::isValidString($key)) {
			throw new Exception("Key must be a non empty scalar");
		}

		self::$params[$key] = $value;
	}

	/**
	 * @param	string	$key
	 * @return	mixed | $default when not found
	 */
	static public function getParam($key, $default = null)
	{
		if (! self::isParam($key)) {
			return $default;
		}

		return self::$params[$key];
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isParam($key)
	{
		if (empty($key) || !is_string($key) || !isset(self::$params[$key])) {
			return false;
		}

		return true;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setDomainMap(array $map)
	{
		if (! empty($map) && $map === array_values($map)) {
			throw new Exception("domain map must be an associative arrays");
		}

		foreach ($map as $key => $class) {
			self::addDomainClass($key, $class);
		}
	}
	
	/**
	 * @return	array
	 */
	static public function getDomainMap()
	{
		return	self::$domains;
	}

	/**
	 * @return null
	 */
	static public function clearDomainMap()
	{
		self::$domains = array();
	}

	/**
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addDomainClass($key, $class)
	{
		if (! self::isValidString($key)) {
			throw new Exception("key must be a non empty string");
		}

		if (! self::isValidString($class)) {
			throw new Exception("class must be a non empty string");
		}

		self::$domains[$key] = $class;
	}

	/**
	 * @param	string	$key	domain key
	 * @return	string
	 */
	static public function getDomainClass($key)
	{
		if (! self::isDomainClass($key)) {
			return false;
		}

		return self::$domains[$key];
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isDomainClass($key)
	{
		if (empty($key) || !is_string($key) || !isset(self::$domains[$key])) {
			return false;
		}

		return true;
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearParams();
		self::clearDomainMap();
		self::clearStartupTasks();
	}

	/** 
	 * No empty strings or keys of just whitespaces 
	 * this also means no numeric keys or string keys like '0'
	 * this strictness helps enforce more meaningful text keys 
	 */
	static protected function isValidString($str)
	{
		if (empty($str) || !is_string($str) || !($tmp = trim($str))) {
			return false;
		}

		return true;
	}
}
