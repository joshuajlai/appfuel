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
namespace Appfuel\Validate;

use DomainException;

/**
 * Creates validators and filters based on a static map.
 */
class ValidationManager implements ValidationManagerInterface
{
	/**
	 * List of key to class name mappings used to create validators
	 * @var	array
	 */
	static protected $validatorMap = array();

	/**
	 * List of key to class name mappings used to create filters
	 * @var array
	 */
	static protected $filterMap = array();

	/**
	 * @var array
	 */	
	static protected $cache = array(
		'validator' => array(),
		'filter'    => array()
	);

	/**
	 * @return	array
	 */
	static public function getValidatorMap()
	{
		return self::$validatorMap;	
	}

	/**
	 * @param	array	$map	
	 * @return	null
	 */
	static public function setValidatorMap(array $map)
	{
		if ($map === array_values($map)) {
			$err  = "validator map must be an associative array of key ";
			$err .= "to validator class name mappings";
			throw new DomainException($err);
		}

		foreach ($map as $key => $value) {
			if (! is_string($key) || empty($key)) {
				$err = "validator key must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($value) || empty($value)) {
				$err = "validator class must be a non empty string";
				throw new DomainException($err);
			}
		}

		self::$validatorMap = $map;
	}

	/**
	 * @return	null
	 */
	static public function clearValidatorMap()
	{
		self::$validatorMap = array();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapValidator($key)
	{
		if (! is_string($key) || ! isset(self::$validatorMap[$key])) {
			return false;
		}

		return self::$validatorMap[$key];
	}

	/**
	 * @param	string	$key
	 * @param	ValidatorInterface	$validator
	 * @return	null
	 */
	static public function addValidatorToCache($key, ValidatorInterface $val)
	{
		if (! self::isValidKey($key)) {
			$err = "validator key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		self::$cache['validator'][$key] = $val;
	}

	/**
	 * @param	string	$key
	 * @return	ValidatorInterface | false
	 */
	static public function getValidatorFromCache($key)
	{
		if (! self::isValidKey($key)) {
			$err = "validator key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if (! isset(self::$cache['validator'][$key])) {
			return false;
		}

		return self::$cache['validator'][$key];
	}

	/**
	 * @return	null
	 */
	static public function clearValidatorCache()
	{
		self::$cache['validator'] = array();
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function getValidator($key)
	{
		$validator = self::getValidatorFromCache($key);
		if ($validator instanceof ValidatorInterface) {
			return $validator;
		}
			
		$class = self::mapValidator($key);
		if (false === $class) {
			$err = "validator -($key) is not mapped";
			throw new DomainException($err);
		}
		$validator = new $class();
		self::addValidatorToCache($key, $validator);

		return $validator;
	}

	/**
	 * @return	array
	 */
	static public function getFilterMap()
	{
		return self::$filterMap;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setFilterMap(array $map)
	{
		if ($map === array_values($map)) {
			$err  = "filter map must be an associative array of key ";
			$err .= "to filter class name mappings";
			throw new DomainException($err);
		}

		foreach ($map as $key => $value) {
			if (! is_string($key) || empty($key)) {
				$err = "filter key must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($value) || empty($value)) {
				$err = "filter class must be a non empty string";
				throw new DomainException($err);
			}
		}

		self::$filterMap = $map;
	}

	/**
	 * @return	null
	 */
	static public function clearFilterMap()
	{
		self::$filterMap = array();
	}

	/**
	 * @param	string	$key
	 * @param	ValidatorInterface	$validator
	 * @return	null
	 */
	static public function addFilterToCache($key, FilterInterface $filter)
	{
		if (! self::isValidKey($key)) {
			$err = "filter key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		self::$cache['filter'][$key] = $filter;
	}

	/**
	 * @param	string	$key
	 * @return	ValidatorInterface | false
	 */
	static public function getFilterFromCache($key)
	{
		if (! self::isValidKey($key)) {
			$err = "filter key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if (! isset(self::$cache['filter'][$key])) {
			return false;
		}

		return self::$cache['filter'][$key];
	}

	/**
	 * @return	null
	 */
	static public function clearFilterCache()
	{
		self::$cache['filter'] = array();
	}

	/**
	 * @param	string $key
	 * @return	mixed
	 */
	static public function getFilter($key)
	{
		$filter = self::getFilterFromCache($key);
		if ($filter instanceof FilterInterface) {
			return $filter;
		}
			
		$class = self::mapFilter($key);
		if (false === $class) {
			$err = "filter -($key) is not mapped";
			throw new DomainException($err);
		}
		$filter = new $class();
		self::addFilterToCache($key, $filter);

		return $filter;
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function mapFilter($key)
	{
		if (! is_string($key) || ! isset(self::$filterMap[$key])) {
			return false;
		}

		return self::$filterMap[$key];
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearValidatorMap();
		self::clearValidatorCache();
		self::clearFilterMap();
		self::clearFilterCache();
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static protected function isValidKey($key)
	{
		if (! is_string($key) || empty($key)) {
			return false;
		}

		return true;
	}
}
