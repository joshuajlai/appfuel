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

use InvalidArgumentException;

/**
 * Decouples the kernal settings from the kernal
 */
class KernelRegistry
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
	 * Holds a list of route key to action namespace mappings
	 * @var array
	 */
	static protected $routes = array();

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
			throw new InvalidArgumentException(
				"params must be an associative arrays"
			);
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
			throw new InvalidArgumentException(
				"Key must be a non empty scalar"
			);
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
	 * Collect all the parameters in the list. List is an associative array
	 * of key => default. When key is not found default used. When default
	 * is 'af-exclude-not-found' then the key is omitted.
	 *
	 * @param	array	$list
	 * @return	array
	 */
	static public function collectParams(array $list)
	{
		$result = array();
		foreach ($list as $key => $default) {
			$param = self::getParam($key, $default);
			if ('af-exclude-not-found' === $param) {
				continue;
			}
			$result[$key] = $param;
		}

		return $result;
	}


	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setDomainMap(array $map)
	{
		if (! empty($map) && $map === array_values($map)) {
			throw new InvalidArgumentException(
				"domain map must be an associative arrays"
			);
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
			throw new InvalidArgumentException(
				"key must be a non empty string"
			);
		}

		if (! self::isValidString($class)) {
			throw new InvalidArgumentException(
				"class must be a non empty string"
			);
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
	 * @param	string	$key	domain key
	 * @return	string
	 */
	static public function getActionNamespace($key)
	{
		if (! is_string($key) || !isset(self::$routes[$key])) {
			return false;
		}

		return self::$routes[$key];
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearParams();
		self::clearDomainMap();
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
