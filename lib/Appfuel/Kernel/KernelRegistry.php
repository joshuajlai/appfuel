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
	 * Strategy used to dertermine the type of output is used. Appfuel 
	 * has the following: console, html, ajax (api soon to come)
	 * @var string
	 */
	static protected $appStrategy = null;

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
	 * @return	string
	 */
	static public function getAppStrategy()
	{
		return self::$appStrategy;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setAppStrategy($name)
	{
		if (empty($name) || ! is_string($name) || ! ($name = trim($name))) {
			$err = 'Application strategy mustt be a non empty string';
			throw new InvalidArgumentException($err);
		}
		
		$name  = strtolower($name);
		$valid = array('console', 'html', 'html-page', 'ajax');
		if (! in_array($name, $valid, true)) {
			$err = "Application strategy must be ";
			$err .= "-(console|html|html-page|ajax)";
			throw new InvalidArgumentException($err);
		}

		self::$appStrategy = $name;
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
	 * @param	array	$map
	 * @return	null
	 */
	static public function setRouteMap(array $map)
	{
		if (! empty($map) && $map === array_values($map)) {
			throw new InvalidArgumentException(
				"route map must be an associative arrays"
			);
		}

		foreach ($map as $key => $class) {
			self::addRoute($key, $class);
		}
	}
	
	/**
	 * @return	array
	 */
	static public function getRouteMap()
	{
		return	self::$routes;
	}

	/**
	 * @return null
	 */
	static public function clearRouteMap()
	{
		self::$routes = array();
	}

	/**
	 * @param	string	$key
	 * @param	string	$class
	 * @return	null
	 */
	static public function addRoute($key, $actionNamespace)
	{
		if (! is_string($key)) {
			throw new InvalidArgumentException(
				"key must be a non empty string"
			);
		}

		if (! self::isValidString($actionNamespace)) {
			throw new InvalidArgumentException(
				"action namespace must be a non empty string"
			);
		}

		self::$routes[$key] = $actionNamespace;
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
		self::clearRouteMap();
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
